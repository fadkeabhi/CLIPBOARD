const clips = document.getElementsByClassName('clip');
let buttonLastCopied;

for (const clip of clips) {
	const button = document.createElement("button");
	setClipboardEmoji(button);
	button.addEventListener('click', () => {
		navigator.clipboard.writeText(clip.children[2].childNodes[0].data) // assumption: the second child element is always the clip content
		.then(() => {
			setTickEmoji(button);
		}, (err) => {
			alert('Error copying text: ', err);
		});
	});
	clip.children[2].appendChild(button);
}

function setClipboardEmoji(button) {
	button.innerHTML = String.fromCodePoint(0x1F4CB); // 📋 (Clipboard emoji)
}

function setTickEmoji(button) {
	if (buttonLastCopied) {
		setClipboardEmoji(buttonLastCopied);
	}
	button.innerHTML = String.fromCodePoint(0x2714); // ✔️ (Tick emoji)
	buttonLastCopied = button;
}