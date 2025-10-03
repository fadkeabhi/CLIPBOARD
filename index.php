<?php
/**
 * Front Controller / Router
 * 
 * This file handles all incoming requests and routes them to appropriate handlers.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/functions.php';

// Get the URL from the query string
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = $url ? explode('/', $url) : [];

// ============================================================================
// HANDLE POST REQUESTS
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'register':
            $result = registerUser($_POST['username'] ?? '', $_POST['email'] ?? '', $_POST['password'] ?? '');
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = $result['success'] ? 'success' : 'danger';
            if ($result['success']) {
                redirect(SITE_URL . '/login');
            } else {
                redirect(SITE_URL . '/register');
            }
            break;
            
        case 'login':
            $result = loginUser($_POST['username'] ?? '', $_POST['password'] ?? '');
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = $result['success'] ? 'success' : 'danger';
            if ($result['success']) {
                redirect(SITE_URL . '/');
            } else {
                redirect(SITE_URL . '/login');
            }
            break;
            
        case 'logout':
            logoutUser();
            redirect(SITE_URL . '/');
            break;
            
        case 'create_board':
            if (!isLoggedIn()) {
                redirect(SITE_URL . '/login');
            }
            $result = createBoard(
                getCurrentUserId(),
                $_POST['name'] ?? '',
                $_POST['default_access'] ?? 'private',
                isset($_POST['is_editable']),
                $_POST['password'] ?? null
            );
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = $result['success'] ? 'success' : 'danger';
            if ($result['success']) {
                redirect(SITE_URL . '/b/' . $result['suburl']);
            } else {
                redirect(SITE_URL . '/');
            }
            break;
            
        case 'add_clip':
            $boardId = $_POST['board_id'] ?? 0;
            $board = getBoardById($boardId);
            
            if (!$board || !canEditBoard(getCurrentUserId(), $board)) {
                $_SESSION['message'] = 'You do not have permission to add clips.';
                $_SESSION['message_type'] = 'danger';
                redirect(SITE_URL . '/');
                break;
            }
            
            $result = addClip($boardId, getCurrentUserId(), $_POST['content'] ?? '');
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = $result['success'] ? 'success' : 'danger';
            redirect(SITE_URL . '/b/' . $board['suburl']);
            break;
            
        case 'edit_clip':
            $clipId = $_POST['clip_id'] ?? 0;
            $clip = getClipById($clipId);
            
            if (!$clip) {
                $_SESSION['message'] = 'Clip not found.';
                $_SESSION['message_type'] = 'danger';
                redirect(SITE_URL . '/');
                break;
            }
            
            $board = getBoardById($clip['board_id']);
            
            if (!canEditClip(getCurrentUserId(), $board, $clip)) {
                $_SESSION['message'] = 'You do not have permission to edit this clip.';
                $_SESSION['message_type'] = 'danger';
                redirect(SITE_URL . '/b/' . $board['suburl']);
                break;
            }
            
            $result = updateClip($clipId, $_POST['content'] ?? '');
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = $result['success'] ? 'success' : 'danger';
            redirect(SITE_URL . '/b/' . $board['suburl']);
            break;
            
        case 'delete_clip':
            $clipId = $_POST['clip_id'] ?? 0;
            $clip = getClipById($clipId);
            
            if (!$clip) {
                $_SESSION['message'] = 'Clip not found.';
                $_SESSION['message_type'] = 'danger';
                redirect(SITE_URL . '/');
                break;
            }
            
            $board = getBoardById($clip['board_id']);
            
            if (!canEditClip(getCurrentUserId(), $board, $clip)) {
                $_SESSION['message'] = 'You do not have permission to delete this clip.';
                $_SESSION['message_type'] = 'danger';
                redirect(SITE_URL . '/b/' . $board['suburl']);
                break;
            }
            
            deleteClip($clipId);
            $_SESSION['message'] = 'Clip deleted successfully!';
            $_SESSION['message_type'] = 'success';
            redirect(SITE_URL . '/b/' . $board['suburl']);
            break;
            
        case 'verify_password':
            $boardId = $_POST['board_id'] ?? 0;
            $board = getBoardById($boardId);
            
            if (!$board) {
                redirect(SITE_URL . '/');
                break;
            }
            
            if (verifyBoardPassword($board, $_POST['password'] ?? '')) {
                redirect(SITE_URL . '/b/' . $board['suburl']);
            } else {
                $_SESSION['message'] = 'Incorrect password.';
                $_SESSION['message_type'] = 'danger';
                redirect(SITE_URL . '/b/' . $board['suburl']);
            }
            break;
            
        case 'update_board_settings':
            $boardId = $_POST['board_id'] ?? 0;
            $board = getBoardById($boardId);
            
            if (!$board || !isBoardAdmin(getCurrentUserId(), $board)) {
                $_SESSION['message'] = 'You do not have permission to edit board settings.';
                $_SESSION['message_type'] = 'danger';
                redirect(SITE_URL . '/');
                break;
            }
            
            $settings = [
                'name' => $_POST['name'] ?? $board['name'],
                'default_access' => $_POST['default_access'] ?? $board['default_access'],
                'is_editable' => isset($_POST['is_editable'])
            ];

            $passwordRemoved = false;
            
            // Handle password updates
            if (isset($_POST['remove_password']) && $_POST['remove_password'] == '1') {
                // Admin wants to remove password protection
                $settings['password'] = '';
                $passwordRemoved = true;
            } elseif (isset($_POST['password']) && !empty($_POST['password'])) {
                // Admin wants to set/change password
                $settings['password'] = $_POST['password'];
            }
            // If neither condition is met, password remains unchanged
            
            $result = updateBoardSettings($boardId, $settings);
            
            // Customize message if password was removed
            if ($result['success'] && $passwordRemoved) {
                $_SESSION['message'] = 'Board settings updated successfully! Password protection has been removed.';
            } else {
                $_SESSION['message'] = $result['message'];
            }

            $_SESSION['message_type'] = $result['success'] ? 'success' : 'danger';
            redirect(SITE_URL . '/b/' . $board['suburl'] . '/settings');
            break;
            
        case 'add_collaborator':
            $boardId = $_POST['board_id'] ?? 0;
            $board = getBoardById($boardId);
            
            if (!$board || !isBoardAdmin(getCurrentUserId(), $board)) {
                $_SESSION['message'] = 'You do not have permission to add collaborators.';
                $_SESSION['message_type'] = 'danger';
                redirect(SITE_URL . '/');
                break;
            }
            
            $userId = $_POST['user_id'] ?? 0;
            $permissionLevel = $_POST['permission_level'] ?? 'view';
            
            $result = setBoardPermission($boardId, $userId, $permissionLevel);
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = $result['success'] ? 'success' : 'danger';
            redirect(SITE_URL . '/b/' . $board['suburl'] . '/settings');
            break;
            
        case 'remove_collaborator':
            $boardId = $_POST['board_id'] ?? 0;
            $board = getBoardById($boardId);
            
            if (!$board || !isBoardAdmin(getCurrentUserId(), $board)) {
                $_SESSION['message'] = 'You do not have permission to remove collaborators.';
                $_SESSION['message_type'] = 'danger';
                redirect(SITE_URL . '/');
                break;
            }
            
            $userId = $_POST['user_id'] ?? 0;
            removeBoardPermission($boardId, $userId);
            
            $_SESSION['message'] = 'Collaborator removed successfully!';
            $_SESSION['message_type'] = 'success';
            redirect(SITE_URL . '/b/' . $board['suburl'] . '/settings');
            break;
            
        case 'delete_board':
            $boardId = $_POST['board_id'] ?? 0;
            $board = getBoardById($boardId);
            
            if (!$board || !isBoardAdmin(getCurrentUserId(), $board)) {
                $_SESSION['message'] = 'You do not have permission to delete this board.';
                $_SESSION['message_type'] = 'danger';
                redirect(SITE_URL . '/');
                break;
            }
            
            deleteBoard($boardId);
            $_SESSION['message'] = 'Board deleted successfully!';
            $_SESSION['message_type'] = 'success';
            redirect(SITE_URL . '/');
            break;

        case 'change_password':
            if (!isLoggedIn()) {
                redirect(SITE_URL . '/login');
            }

            $current = $_POST['current_password'] ?? '';
            $new = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if ($new !== $confirm) {
                $_SESSION['message'] = 'New password and confirmation do not match.';
                $_SESSION['message_type'] = 'danger';
                redirect(SITE_URL . '/profile');
            }

            $result = changeUserPassword(getCurrentUserId(), $current, $new);
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = $result['success'] ? 'success' : 'danger';
            redirect(SITE_URL . '/profile');
            break;
    }
}

// ============================================================================
// ROUTE REQUESTS
// ============================================================================

// Home page
if (empty($url)) {
    $userBoards = isLoggedIn() ? getUserBoards(getCurrentUserId()) : [];
    include __DIR__ . '/templates/header.php';
    include __DIR__ . '/templates/home.php';
    include __DIR__ . '/templates/footer.php';
    exit;
}

// Login page
if ($url === 'login') {
    if (isLoggedIn()) {
        redirect(SITE_URL . '/');
    }
    include __DIR__ . '/templates/header.php';
    include __DIR__ . '/templates/login.php';
    include __DIR__ . '/templates/footer.php';
    exit;
}

// Profile page
if ($url === 'profile') {
    if (!isLoggedIn()) {
        redirect(SITE_URL . '/login');
    }
    include __DIR__ . '/templates/header.php';
    include __DIR__ . '/templates/profile.php';
    include __DIR__ . '/templates/footer.php';
    exit;
}

// Register page
if ($url === 'register') {
    if (isLoggedIn()) {
        redirect(SITE_URL . '/');
    }
    include __DIR__ . '/templates/header.php';
    include __DIR__ . '/templates/register.php';
    include __DIR__ . '/templates/footer.php';
    exit;
}

// Board routes: /b/{suburl} or /b/{suburl}/settings
if ($urlParts[0] === 'b' && isset($urlParts[1])) {
    $suburl = $urlParts[1];
    $board = getBoardBySuburl($suburl);
    
    if (!$board) {
        $_SESSION['message'] = 'Board not found.';
        $_SESSION['message_type'] = 'danger';
        redirect(SITE_URL . '/');
    }
    
    $userPermission = getUserPermission(getCurrentUserId(), $board);
    
    // Check if user needs to enter password
    if (!canViewBoard(getCurrentUserId(), $board) && !empty($board['password_hash'])) {
        include __DIR__ . '/templates/header.php';
        include __DIR__ . '/templates/password_prompt.php';
        include __DIR__ . '/templates/footer.php';
        exit;
    }
    
    // Check if user has view permission
    if (!canViewBoard(getCurrentUserId(), $board)) {
        $_SESSION['message'] = 'You do not have permission to view this board.';
        $_SESSION['message_type'] = 'danger';
        redirect(SITE_URL . '/');
    }
    
    // Board settings page
    if (isset($urlParts[2]) && $urlParts[2] === 'settings') {
        if (!isBoardAdmin(getCurrentUserId(), $board)) {
            $_SESSION['message'] = 'You do not have permission to access board settings.';
            $_SESSION['message_type'] = 'danger';
            redirect(SITE_URL . '/b/' . $board['suburl']);
        }
        
        $collaborators = getBoardCollaborators($board['id']);
        $allUsers = searchUsers();
        
        include __DIR__ . '/templates/header.php';
        include __DIR__ . '/templates/board_settings.php';
        include __DIR__ . '/templates/footer.php';
        exit;
    }
    
    // Board view page
    $clips = getBoardClips($board['id']);
    
    include __DIR__ . '/templates/header.php';
    include __DIR__ . '/templates/board.php';
    include __DIR__ . '/templates/footer.php';
    exit;
}

// 404 - Page not found
$_SESSION['message'] = 'Page not found.';
$_SESSION['message_type'] = 'danger';
redirect(SITE_URL . '/');
