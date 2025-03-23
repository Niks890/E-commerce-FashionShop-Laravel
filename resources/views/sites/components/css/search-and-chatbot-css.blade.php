<style>
.modal-container-search {
    position: relative;
    max-height: 80vh;
    overflow-y: auto;
    padding-right: 15px;
}

.modal-close {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1100;
    background: white;
    padding: 5px;
    border-radius: 50%;
    cursor: pointer;
}

.modal-close i {
    font-size: 1.5rem;
    color: #6c757d;
}

.modal-close:hover i {
    color: #dc3545;
}

#search-history li {
    font-size: 18px;
    padding: 12px 16px;
    border-bottom: 1px solid #ddd;
    transition: background 0.2s;
}

#search-history li:hover {
    background: #f1f1f1;
}

#search-history li i {
    font-size: 20px;
}

#search-history li .text-primary {
    font-size: 16px;
    cursor: pointer;
}

#suggestion-list li {
    font-size: 18px;
    transition: background 0.2s;
}

#suggestion-list li:hover {
    background: #f9f9f9;
    cursor: pointer;
}
</style>