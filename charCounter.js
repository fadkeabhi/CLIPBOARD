const textArea = document.querySelector("#clipInput");
const submitInput = document.querySelector("#submitInput");
const charCount = document.querySelector("#charCount");

submitInput.disabled = true;

textArea.addEventListener("keyup", ({ target }) => {
  const length = target.value.length;
  length > 0 ? (submitInput.disabled = false) : (submitInput.disabled = true);
  charCount.innerHTML = target.value.length;
});
