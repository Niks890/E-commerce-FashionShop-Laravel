$(document).ready(function() {

    const addBtns = document.querySelectorAll(".add-new-modal");
    const modal = document.querySelector(".js-modal");
    const modalContainer = document.querySelector(".js-modal-container");
    const modalClose = document.querySelector(".js-modal-close");

    function showModal() {
        if (modal) {
            modal.classList.add("open");
        }

        const modalHeader = document.querySelector('.modal-header');
        if (modalHeader) {
            modalHeader.textContent = "Thêm Chương trình khuyến mãi";
        }

        const nameInput = document.querySelector('#name');
        if (nameInput) {
            nameInput.value = "";
        }

        const percentInput = document.querySelector('#percent_discount');
        if (percentInput) {
            percentInput.value = "";
        }

        const startDateInput = document.querySelector('#start_date');
        if (startDateInput) {
            startDateInput.value = "";
        }

        const endDateInput = document.querySelector('#end_date');
        if (endDateInput) {
            endDateInput.value = "";
        }
    }

    function hideModal() {
        if (modal) {
            modal.classList.remove("open");
        }
    }

    // Kiểm tra và thêm event listener cho các nút add
    addBtns.forEach(function(addBtn) {
        if (addBtn) {
            addBtn.addEventListener("click", showModal);
        }
    });

    // Kiểm tra và thêm event listener cho nút close
    if (modalClose) {
        modalClose.addEventListener("click", hideModal);
    }

});
