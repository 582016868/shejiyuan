// 各页面下的 tab 导航条点击加 active 类 
$(function() {
    //Simple filter controls
    $('.simplefilter li').click(function() {
        $('.simplefilter li').removeClass('active');
        $(this).addClass('active');
    });
    //Multifilter controls
    $('.multifilter li').click(function() {
        $(this).toggleClass('active');
    });
    //Shuffle control
    $('.shuffle-btn').click(function() {
        $('.sort-btn').removeClass('active');
    });
    //Sort controls
    $('.sort-btn').click(function() {
        $('.sort-btn').removeClass('active');
        $(this).addClass('active');
    });
});
