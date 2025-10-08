<?php
/**
 * Core Functions for Clipboard Application
 * 
 * This file contains all the business logic including:
 * - User authentication
 * - Permission checking
 * - Board and clip management
 */

require_once __DIR__ . '/../config.php';

/**
 * Get the PDO database connection
 */
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = require __DIR__ . '/db.php';
    }
    return $pdo;
}

// ============================================================================
// USER AUTHENTICATION FUNCTIONS
// ============================================================================

/**
 * Register a new user
 * 
 * @param string $username
 * @param string $email
 * @param string $password
 * @return array ['success' => bool, 'message' => string]
 */
function registerUser($username, $email, $password) {
    $pdo = getDB();
    
    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'All fields are required.'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format.'];
    }
    
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters.'];
    }
    
    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $passwordHash]);
        
        return ['success' => true, 'message' => 'Registration successful!'];
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            return ['success' => false, 'message' => 'Username or email already exists.'];
        }
        return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
    }
}

/**
 * Login user
 * 
 * @param string $username
 * @param string $password
 * @return array ['success' => bool, 'message' => string]
 */
function loginUser($username, $password) {
    $pdo = getDB();
    
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'All fields are required.'];
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            return ['success' => true, 'message' => 'Login successful!'];
        } else {
            return ['success' => false, 'message' => 'Invalid username or password.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
    }
}

/**
 * Logout user
 */
function logoutUser() {
    session_destroy();
    session_start();
}

/**
 * Check if user is logged in
 * 
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user ID
 * 
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get user by ID
 *
 * @param int $userId
 * @return array|null
 */
function getUserById($userId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

/**
 * Change user's password
 *
 * @param int $userId
 * @param string $currentPassword
 * @param string $newPassword
 * @return array ['success' => bool, 'message' => string]
 */
function changeUserPassword($userId, $currentPassword, $newPassword) {
    $pdo = getDB();

    if (empty($currentPassword) || empty($newPassword)) {
        return ['success' => false, 'message' => 'All fields are required.'];
    }

    if (strlen($newPassword) < 6) {
        return ['success' => false, 'message' => 'New password must be at least 6 characters.'];
    }

    try {
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        if (!password_verify($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Current password is incorrect.'];
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $update->execute([$newHash, $userId]);

        return ['success' => true, 'message' => 'Password changed successfully.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to change password: ' . $e->getMessage()];
    }
}

// ============================================================================
// BOARD MANAGEMENT FUNCTIONS
// ============================================================================

/**
 * Create a new board
 * 
 * @param int $ownerId
 * @param string $name
 * @param string $defaultAccess
 * @param bool $isEditable
 * @param string|null $password
 * @return array ['success' => bool, 'message' => string, 'suburl' => string|null]
 */
function createBoard($ownerId, $name, $defaultAccess = 'private', $isEditable = true, $password = null, $listPublically = false) {
    $pdo = getDB();
    
    if (empty($name)) {
        return ['success' => false, 'message' => 'Board name is required.'];
    }
    
    // Generate unique suburl
    $suburl = generateSuburl($name);
    $originalSuburl = $suburl;
    $counter = 1;
    
    // Ensure suburl is unique
    while (boardSuburlExists($suburl)) {
        $suburl = $originalSuburl . '-' . $counter;
        $counter++;
    }
    
    // Hash password if provided
    $passwordHash = $password ? password_hash($password, PASSWORD_DEFAULT) : null;
    // If board is private, it cannot be publicly listed
    if ($defaultAccess === 'private') {
        $listPublically = 0;
    } else {
        // Normalize list_publically to integer
        $listPublically = $listPublically ? 1 : 0;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO boards (owner_id, name, suburl, default_access, list_publically, is_editable, password_hash) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
    $stmt->execute([$ownerId, $name, $suburl, $defaultAccess, $listPublically, $isEditable ? 1 : 0, $passwordHash]);
        
        return ['success' => true, 'message' => 'Board created successfully!', 'suburl' => $suburl];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to create board: ' . $e->getMessage()];
    }
}

/**
 * Generate URL-friendly suburl from board name
 * 
 * @param string $name
 * @return string
 */
function generateSuburl($name) {
    $suburl = strtolower(trim($name));
    $suburl = preg_replace('/[^a-z0-9-]/', '-', $suburl);
    $suburl = preg_replace('/-+/', '-', $suburl);
    $suburl = trim($suburl, '-');
    return $suburl ?: 'board';
}

/**
 * Check if suburl exists
 * 
 * @param string $suburl
 * @return bool
 */
function boardSuburlExists($suburl) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id FROM boards WHERE suburl = ?");
    $stmt->execute([$suburl]);
    return $stmt->fetch() !== false;
}

/**
 * Get board by suburl
 * 
 * @param string $suburl
 * @return array|null
 */
function getBoardBySuburl($suburl) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM boards WHERE suburl = ?");
    $stmt->execute([$suburl]);
    return $stmt->fetch();
}

/**
 * Get board by ID
 * 
 * @param int $boardId
 * @return array|null
 */
function getBoardById($boardId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM boards WHERE id = ?");
    $stmt->execute([$boardId]);
    return $stmt->fetch();
}

/**
 * Update board settings
 * 
 * @param int $boardId
 * @param array $settings
 * @return array ['success' => bool, 'message' => string]
 */
function updateBoardSettings($boardId, $settings) {
    $pdo = getDB();
    
    $fields = [];
    $params = [];
    
    if (isset($settings['name'])) {
        $fields[] = "name = ?";
        $params[] = $settings['name'];
    }
    
    if (isset($settings['default_access'])) {
        $fields[] = "default_access = ?";
        $params[] = $settings['default_access'];
    }
    
    if (isset($settings['is_editable'])) {
        $fields[] = "is_editable = ?";
        $params[] = $settings['is_editable'] ? 1 : 0;
    }

    if (isset($settings['list_publically'])) {
        // Requested value normalized
        $wantsList = $settings['list_publically'] ? 1 : 0;

        // Determine effective default access: prefer provided new value, otherwise fetch current from DB
        if (isset($settings['default_access'])) {
            $effectiveAccess = $settings['default_access'];
        } else {
            // fetch current default_access from DB
            $stmtAcc = $pdo->prepare("SELECT default_access FROM boards WHERE id = ?");
            $stmtAcc->execute([$boardId]);
            $row = $stmtAcc->fetch();
            $effectiveAccess = $row['default_access'] ?? 'private';
        }

        // If effective access is private, force no listing
        if ($effectiveAccess === 'private') {
            $wantsList = 0;
        }

        $fields[] = "list_publically = ?";
        $params[] = $wantsList;
    }
    
    if (isset($settings['password'])) {
        if (empty($settings['password'])) {
            $fields[] = "password_hash = NULL";
            // Clear any session-based password access for this board
            if (isset($_SESSION['board_password_access'][$boardId])) {
                unset($_SESSION['board_password_access'][$boardId]);
            }
        } else {
            $fields[] = "password_hash = ?";
            $params[] = password_hash($settings['password'], PASSWORD_DEFAULT);
        }
    }
    
    if (empty($fields)) {
        return ['success' => false, 'message' => 'No fields to update.'];
    }
    
    $params[] = $boardId;
    
    try {
        $sql = "UPDATE boards SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return ['success' => true, 'message' => 'Board settings updated successfully!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to update settings: ' . $e->getMessage()];
    }
}


/**
 * Get public boards optionally filtered by search text and access type
 *
 * @param string|null $search
 * @param string|null $type one of 'private','public_view','public_add' or null for any
 * @return array
 */
function getPublicBoards($search = null, $type = null) {
    $pdo = getDB();
    $sql = "SELECT * FROM boards WHERE list_publically = 1";
    $params = [];

    if ($type) {
        $sql .= " AND default_access = ?";
        $params[] = $type;
    }

    if ($search) {
        $sql .= " AND (name LIKE ? OR suburl LIKE ?)";
        $params[] = "%" . $search . "%";
        $params[] = "%" . $search . "%";
    }

    $sql .= " ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Delete a board
 * 
 * @param int $boardId
 * @return bool
 */
function deleteBoard($boardId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM boards WHERE id = ?");
    return $stmt->execute([$boardId]);
}

/**
 * Get user's boards
 * 
 * @param int $userId
 * @return array
 */
function getUserBoards($userId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM boards WHERE owner_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// ============================================================================
// PERMISSION MANAGEMENT FUNCTIONS
// ============================================================================

/**
 * Get user's permission level for a board
 * This is the critical permission checking function
 * 
 * @param int|null $userId User ID (null for guests)
 * @param array $board Board data array
 * @return string|null Permission level: 'view', 'edit', 'admin', or null if no access
 */
function getUserPermission($userId, $board) {
    $pdo = getDB();
    
    // Owner has admin access
    if ($userId && $board['owner_id'] == $userId) {
        return 'admin';
    }
    
    // Check for explicit user permission
    if ($userId) {
        $stmt = $pdo->prepare("
            SELECT permission_level 
            FROM board_permissions 
            WHERE board_id = ? AND user_id = ?
        ");
        $stmt->execute([$board['id'], $userId]);
        $permission = $stmt->fetch();
        
        if ($permission) {
            return $permission['permission_level'];
        }
    }
    
    // Check if user has temporary password access (stored in session)
    if (isset($_SESSION['board_password_access'][$board['id']])) {
        return 'view';
    }
    
    // Fall back to board's default public access
    // 'private' returns null (no access)
    // 'public_view' returns 'view'
    // 'public_add' returns 'edit' (can view and add)
    switch ($board['default_access']) {
        case 'public_view':
            return 'view';
        case 'public_add':
            return 'edit';
        default:
            return null;
    }
}

/**
 * Check if user can view board
 * 
 * @param int|null $userId
 * @param array $board
 * @return bool
 */
function canViewBoard($userId, $board) {
    $permission = getUserPermission($userId, $board);
    return $permission !== null;
}

/**
 * Check if user can add/edit clips on board
 * 
 * @param int|null $userId
 * @param array $board
 * @return bool
 */
function canEditBoard($userId, $board) {
    $permission = getUserPermission($userId, $board);
    return in_array($permission, ['edit', 'admin']);
}

/**
 * Check if user is board admin
 * 
 * @param int|null $userId
 * @param array $board
 * @return bool
 */
function isBoardAdmin($userId, $board) {
    if (!$userId) {
        return false;
    }
    $permission = getUserPermission($userId, $board);
    return $permission === 'admin';
}

/**
 * Verify board password
 * 
 * @param array $board
 * @param string $password
 * @return bool
 */
function verifyBoardPassword($board, $password) {
    if (empty($board['password_hash'])) {
        return true; // No password set
    }
    
    if (password_verify($password, $board['password_hash'])) {
        // Grant temporary access in session
        if (!isset($_SESSION['board_password_access'])) {
            $_SESSION['board_password_access'] = [];
        }
        $_SESSION['board_password_access'][$board['id']] = true;
        return true;
    }
    
    return false;
}

/**
 * Add or update board permission for a user
 * 
 * @param int $boardId
 * @param int $userId
 * @param string $permissionLevel 'view', 'edit', or 'admin'
 * @return array ['success' => bool, 'message' => string]
 */
function setBoardPermission($boardId, $userId, $permissionLevel) {
    $pdo = getDB();
    
    if (!in_array($permissionLevel, ['view', 'edit', 'admin'])) {
        return ['success' => false, 'message' => 'Invalid permission level.'];
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO board_permissions (board_id, user_id, permission_level) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE permission_level = ?
        ");
        $stmt->execute([$boardId, $userId, $permissionLevel, $permissionLevel]);
        
        return ['success' => true, 'message' => 'Permission updated successfully!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to set permission: ' . $e->getMessage()];
    }
}

/**
 * Remove board permission for a user
 * 
 * @param int $boardId
 * @param int $userId
 * @return bool
 */
function removeBoardPermission($boardId, $userId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM board_permissions WHERE board_id = ? AND user_id = ?");
    return $stmt->execute([$boardId, $userId]);
}

/**
 * Get all collaborators for a board
 * 
 * @param int $boardId
 * @return array
 */
function getBoardCollaborators($boardId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("
        SELECT u.id, u.username, bp.permission_level 
        FROM board_permissions bp
        JOIN users u ON bp.user_id = u.id
        WHERE bp.board_id = ?
        ORDER BY bp.permission_level DESC, u.username
    ");
    $stmt->execute([$boardId]);
    return $stmt->fetchAll();
}

// ============================================================================
// CLIP MANAGEMENT FUNCTIONS
// ============================================================================

/**
 * Add a clip to a board
 * 
 * @param int $boardId
 * @param int|null $userId
 * @param string $content
 * @return array ['success' => bool, 'message' => string]
 */
function addClip($boardId, $userId, $content) {
    $pdo = getDB();
    
    if (empty($content)) {
        return ['success' => false, 'message' => 'Clip content cannot be empty.'];
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO clips (board_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$boardId, $userId, $content]);
        
        return ['success' => true, 'message' => 'Clip added successfully!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to add clip: ' . $e->getMessage()];
    }
}

/**
 * Update a clip
 * 
 * @param int $clipId
 * @param string $content
 * @return array ['success' => bool, 'message' => string]
 */
function updateClip($clipId, $content) {
    $pdo = getDB();
    
    if (empty($content)) {
        return ['success' => false, 'message' => 'Clip content cannot be empty.'];
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE clips SET content = ? WHERE id = ?");
        $stmt->execute([$content, $clipId]);
        
        return ['success' => true, 'message' => 'Clip updated successfully!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to update clip: ' . $e->getMessage()];
    }
}

/**
 * Delete a clip
 * 
 * @param int $clipId
 * @return bool
 */
function deleteClip($clipId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM clips WHERE id = ?");
    return $stmt->execute([$clipId]);
}

/**
 * Get clip by ID
 * 
 * @param int $clipId
 * @return array|null
 */
function getClipById($clipId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM clips WHERE id = ?");
    $stmt->execute([$clipId]);
    return $stmt->fetch();
}

/**
 * Get all clips for a board
 * 
 * @param int $boardId
 * @return array
 */
function getBoardClips($boardId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("
        SELECT c.*, u.username 
        FROM clips c
        LEFT JOIN users u ON c.user_id = u.id
        WHERE c.board_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$boardId]);
    return $stmt->fetchAll();
}

/**
 * Check if user can edit a specific clip
 * 
 * @param int|null $userId
 * @param array $board
 * @param array $clip
 * @return bool
 */
function canEditClip($userId, $board, $clip) {
    // If board is not editable, only admins can edit
    if (!$board['is_editable']) {
        return isBoardAdmin($userId, $board);
    }
    
    // Otherwise, anyone with edit permission can edit
    return canEditBoard($userId, $board);
}

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

/**
 * Escape HTML output
 * 
 * @param string $text
 * @return string
 */
function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a URL
 * 
 * @param string $url
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Get all users (for collaborator assignment)
 * 
 * @param string $search Optional search term
 * @return array
 */
function searchUsers($search = '') {
    $pdo = getDB();
    
    if (empty($search)) {
        $stmt = $pdo->prepare("SELECT id, username, email FROM users ORDER BY username LIMIT 50");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT id, username, email 
            FROM users 
            WHERE username LIKE ? OR email LIKE ?
            ORDER BY username 
            LIMIT 50
        ");
        $searchTerm = '%' . $search . '%';
        $stmt->execute([$searchTerm, $searchTerm]);
    }
    
    return $stmt->fetchAll();
}
