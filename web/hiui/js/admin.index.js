$(function(){
	//为菜单添加单机事件
    function bindNavbarClick()
    {
        $("#J_navList  li").on('click',function(event){
            event.preventDefault();
            if($(this).find("li").length == 0)
            {
                //取消其他选项的选择状态
                $("#J_navList li.active").removeClass("active").parents("li.opened").removeClass('opened');
                $(this).addClass('active').parents('li').addClass("opened");
                var title = $(this).find('a:first-child').attr("title");
                var href = $(this).find('a:first-child').attr("href");
                var html_breadcrumb = '';
                if(title == "首页")
                {
                    $(window).attr('location','/');
                }else {
                    var html_breadcrumb = '<li class="active">'+$(this).find('span').html()+'</li>';
                    $(".be-main .main-content:first-child").load(href);
                }

                var parent = $(this).parents("li:first");
                html_breadcrumb = '<li><a href="'+$("#J_navList li:first-child").find('a:first-child').attr('href')+'">'+$("#J_navList li:first-child").find('a:first-child').find('span').html()+'</a></li>'+html_breadcrumb;
                // while(parent.parents("ul:first").attr('id') != 'J_navList'){
                //     html_breadcrumb = '<li><a href="'+parent.find('a:first-child').attr('href')+'">'+parent.find('a:first-child').find('span').html()+'</a></li>'+html_breadcrumb;
                //     parent = parent.parents("li:first");
                // }

                $('.be-breadcrumb.breadcrumb').html(html_breadcrumb);
                //收缩其他选项---后期优化
                //$("#J_navList > li:not(.opened)").find("a[aria-expanded='true']").click();
                //$(this).parents("li:first").has(":not(.opened)").find("a[aria-expanded='true']").click();
            }else {
                $("#J_navList li.opened").removeClass("opened");
                $(this).addClass('opened').parents('li').addClass("opened");

            }
            //event.preventDefault();
        });
    }
    bindNavbarClick();
});	