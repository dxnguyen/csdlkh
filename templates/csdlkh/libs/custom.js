function modal_load(url) {
    $('#mydata .modal-content').load(url);
};

if (!Date.now) {
    Date.now = function now() {
        return new Date().getTime();
    };
}