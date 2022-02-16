const checkbox = el_class => {
    $(`.${el_class}`).on("click", function() {
        if (!$(this).hasClass("active")) {
            $(this).addClass("active");
        } else {
            $(this).removeClass("active");
        }
    });

    $(`.${el_class}-text`).on("click", function() {
        let _this = $(this);
        _this.siblings("." + el_class).click();
    });

    // $(`.${el_classText}`).on("click", function() {
    //     if (this.parentNode.firstElementChild.classList.contains("checkbox")) {
    //         const checkbox = this.parentNode.firstElementChild;
    //
    //         if (!$(checkbox).hasClass("active")) {
    //             $(checkbox).addClass("active");
    //         } else {
    //             $(checkbox).removeClass("active");
    //         }
    //     }
    // });
};

module.exports = {
    checkbox: checkbox
};
