const addBtns = document.querySelectorAll(".add-new-modal");
const modal = document.querySelector(".js-modal");
const modalContainer = document.querySelector(".js-modal-container");
const modalClose = document.querySelector(".js-modal-close");

function showModal() {
    modal.classList.add("open");
    document.querySelector('.modal-header').textContent = "Thêm Chương trình khuyến mãi";
    document.querySelector('#name').value = "";
    document.querySelector('#percent_discount').value = "";
    document.querySelector('#start_date').value = "";
    document.querySelector('#end_date').value = "";
}

function hideModal() {
    modal.classList.remove("open");
}

for (const addBtn of addBtns) {
    addBtn.addEventListener("click", showModal);
}

modalClose.addEventListener("click", hideModal);
