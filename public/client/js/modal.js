const searchBtn = document.querySelector(".search-btn");
const modal = document.querySelector(".js-modal");
const modalContainer = document.querySelector(".js-modal-container");
const modalClose = document.querySelector(".js-modal-close");

function showModal() {
    modal.classList.add("open");
}

function hideModal() {
    modal.classList.remove("open");
}

searchBtn.addEventListener("click", showModal);

modalClose.addEventListener("click", hideModal);
