$(document).ready(function () {
    $("#navbarDropdown").click(function () {
        if($(".dropdown-menu").css('display') == "none"){
            $(".dropdown-menu").css("display", "block");
        }else{
            $(".dropdown-menu").css("display", "none");
        }
    });
});