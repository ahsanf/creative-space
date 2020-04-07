$(function () {
    var items = $('#settings>ul>li').each(function () {
        $(this).click(function () {
            //remove previous class and add it to clicked tab
            items.removeClass('current');
            $(this).addClass('current');

            //hide all content divs and show current one
            $('#settings>div.tab-content').hide().eq(items.index($(this))).show();
            window.location.hash = $(this).attr('tab');
        });
    });
    if (location.hash) { showTab(location.hash); }
    else { showTab("tab1"); }
    function showTab(tab) { $("#settings ul li[tab=" + tab + "]").click(); }
    // Bind the event hashchange, using jquery-hashchange-plugin
    $(window).hashchange(function () { showTab(location.hash.replace("#", "")); })
    // Trigger the event hashchange on page load, using jquery-hashchange-plugin
    $(window).hashchange();
});

$(document).ready(function(){
        // ----- Create Menu Scripts
        // ----- Create Menu Scripts
        // ----- Create Menu Scripts
        // Function to Reorder Menu (when remove)
        function reorderMenu( namelist ){
            var list =	$(namelist);
            var nMenu = '';
            list.find("tr.new-menu").each(function(){
                nMenu++;
                //alert('data-number: '+$(this).attr('data-number')+' | nMenu: '+nMenu);
                if ( ($(this).attr('data-number')) != nMenu ) {
                    // Change data-number of <tr>
                    $(this).attr('data-number', nMenu);

                    // Change data-number of <tr><td>
                    $(this).children('td.nMenu').html(nMenu);

                    // Change data-number of <tr><td> > inputs and buttons
                    $(this).children('td').children('input.menu').attr('name','header[menudata]['+(nMenu-1)+'][text]');
                    $(this).children('td').children('input.link').attr('name','header[menudata]['+(nMenu-1)+'][link]');
                    $(this).children('td').children('div.button').attr('data-remove','new-menu-'+(nMenu-1)+'');
                }
            });
        }
        // Add a new menu text/link
        $("#add-new-menu").click(function(){
            var html =	'';
            var list = $("#menu-list");
            var numberMenu = '0';
            list.find("tr").each(function(){ numberMenu++; });
            html +=	'<tr class="new-menu" data-number="'+numberMenu+'" style="display:none;">';
            html +=	'<td class="nMenu">'+numberMenu+'</td>';
            html +=	'<td><input type="text" class="menu" name="header[menudata]['+(numberMenu-1)+'][text]" value="" /></td>';
            html +=	'<td><input type="text" class="link" name="header[menudata]['+(numberMenu-1)+'][link]" value="http://" /></td>';
            html +=	'<td><div class="button remove" data-remove="new-menu-'+(numberMenu-1)+'">Remove</div>';
            html +=	'</tr>';
            list.append(html);
            list.find("tr").fadeIn(600);
        });
        // Remove a new menu
        $(".new-menu .remove").live('click', function(){
            $(this).parent().parent().fadeOut(600).remove();
            reorderMenu('#menu-list');
        });

        // ----- Create Useful Links Scripts
        // ----- Create Useful Links Scripts
        // ----- Create Useful Links Scripts
        // Function to Reorder Links (when remove)
        function reorderLink( namelist ){
                var list =	$(namelist);
                var nLink = '';
                list.find("tr.new-link").each(function(){
                    nLink++;
                    //alert('data-number: '+$(this).attr('data-number')+' | nLink: '+nLink);
                    if ( ($(this).attr('data-number')) != nLink ) {
                        // Change data-number of <tr>
                        $(this).attr('data-number', nLink);

                        // Change data-number of <tr><td>
                        $(this).children('td.nLink').html(nLink);

                        // Change data-number of <tr><td> > inputs and buttons
                        $(this).children('td').children('input.menu').attr('name','footermod_links[footermod_links]['+(nLink-1)+'][text]');
                        $(this).children('td').children('input.link').attr('name','footermod_links[footermod_links]['+(nLink-1)+'][link]');
                        $(this).children('td').children('div.button').attr('data-remove','new-link-'+(nLink-1)+'');
                    }
                });
        }
        // Add a new link
        $("#add-new-link").click(function(){
                var html =	'';
                var list = $("#link-list");
                var numLink = '0';
                list.find("tr").each(function(){ numLink++; });
                html +=	'<tr class="new-link" data-number="'+numLink+'" style="display:none;">';
                html +=	'<td class="nLink">'+numLink+'</td>';
                html +=	'<td><input type="text" class="menu" name="footermod_links[footermod_links]['+(numLink-1)+'][text]" value="" /></td>';
                html +=	'<td><input type="text" class="link" name="footermod_links[footermod_links]['+(numLink-1)+'][link]" value="http://" /></td>';
                html +=	'<td><div class="button remove" data-remove="new-link-'+(numLink-1)+'">Remove</div>';
                html +=	'</tr>';
                list.append(html);
                list.find("tr").fadeIn(600);
        });
        // Remove a new link
        $(".new-link .remove").live('click', function(){
                $(this).parent().parent().fadeOut(600).remove();
                reorderLink('#link-list');
        });

        // ----- Create Slider Scripts
        // Function to Reorder Slides (when remove)
        function reorderSlide( namelist ){
                var list =	$(namelist);
                var nSlide = '';
                list.find("tr.new-slide").each(function(){
                        nSlide++;
                        //alert('data-number: '+$(this).attr('data-number')+' | nSlide: '+nSlide);
                        if ( ($(this).attr('data-number')) != nSlide ) {
                                // Change data-number of <tr>
                                $(this).attr('data-number', nSlide);
                                // Change data-number of <tr><td> > inputs and buttons
                                $(this).children('td').children('label').children('input.name').attr('name','frontpage[slideshowdata]['+(nSlide-1)+'][title]');
                                $(this).children('td').children('label').children('input.link').attr('name','frontpage[slideshowdata]['+(nSlide-1)+'][link]');
                                $(this).children('td').children('label').children('input.imag').attr('name','frontpage[slideshowdata]['+(nSlide-1)+'][image]');
                                $(this).children('td').children('label').children('textarea.desc').attr('name','frontpage[slideshowdata]['+(nSlide-1)+'][description]');
                                $(this).children('td').children('div.button').attr('data-remove','new-slide-'+(nSlide-1)+'');
                        }
                });
        }
        // Add a new slider text/link
        $("#add-new-slide").click(function(){
                var html =	'';
                var list = $("#slide-list");
                var numSlide = '1';
                list.find("tr").each(function(){ numSlide++; });
                if (numSlide == '1') { fst = ' first'; } else { fst = ''; }
                html +=	'<tr class="new-slide'+fst+'" data-number="'+numSlide+'" style="display:none;">';
                html +=	'	<td><label class="left first">Title: ';
                html += '		<input type="text" class="name" name="frontpage[slideshowdata]['+(numSlide-1)+'][title]" value="" />';
                html += '	</label><label class="left">Link (URL): ';
                html += '		<input type="text" class="link" name="frontpage[slideshowdata]['+(numSlide-1)+'][link]" value="http://" />';
                html += '	</label><label class="left">Image: ';
                html += '		<input type="text" class="imag" name="frontpage[slideshowdata]['+(numSlide-1)+'][image]" value="http://" />';
                html += '	</label></td>';
                html += '	<td><label>Description: ';
                html += '		<textarea class="desc" name="frontpage[slideshowdata]['+(numSlide-1)+'][description]"></textarea>';
                html += '	</label><div class="button remove" data-remove="new-slide-'+(numSlide-1)+'">';
                html += '		Remove slide item';
                html += '	</div></td></tr>';
                list.append(html);
                list.find("tr").fadeIn(600);
        });
        // Remove a new slide
        $(".new-slide .remove").live('click', function(){
                $(this).parent().parent().fadeOut(600).remove();
                reorderSlide('#slide-list');
        });

        // ----- Create Featured Courses Scripts
        // ----- Create Featured Courses Scripts
        // ----- Create Featured Courses Scripts
        // Function to Reorder Featured Courses (when remove)
        function reorderFCourse( namelist ){
            var list =	$(namelist);
            var nFCourse = '0';
            list.find("tr.new-fcourse").each(function(){
                nFCourse++;
                //alert('data-number: '+$(this).attr('data-number')+' | nLink: '+nLink);
                if ( ($(this).attr('data-number')) != nFCourse ) {
                    // Change data-number of <tr>
                    $(this).attr('data-number', nFCourse);

                    // Change data-number of <tr><td>
                    $(this).children('td.nFCourse').html(nFCourse);

                    // Change data-number of <tr><td> > inputs and buttons
                    $(this).children('td').children('select.name').attr('name',	'frontpage[featuredcourses]['+(nFCourse-1)+']');
                    $(this).children('td').children('div.button').attr('data-remove','new-fcourse-'+(nFCourse-1)+'');
                }
            });
        }
        // Add a new Featured Course
        $("#add-new-fcourse").click(function(){
            $.ajax({
                url: "coursesajax.php",
                success:function(data) {
                    var html =	'';
                    var list = $("#featured-course-list");
                    var numFCourse = '0';
                    list.find("tr").each(function(){ numFCourse++; });
                    html +=	'<tr class="new-fcourse" data-number="'+numFCourse+'" style="display:none;">';
                    html +=	'	<td class="nFCourse">'+numFCourse+'</td>';
                    html +=	'	<td>';
                    html +=	'		<select class="name" name="frontpage[featuredcourses]['+(numFCourse-1)+']">';
                    html +=	data;
                    html +=	'		</select>';
                    html +=	'	</td>';
                    html +=	'	<td><div class="button remove" data-remove="new-fcourse-'+(numFCourse-1)+'">Remove</div>';
                    html +=	'</tr>';
                    list.append(html);
                    list.find("tr").fadeIn(600);
                }
            });
        });
        // Remove a new Featured Course
        $(".new-fcourse .remove").live('click', function(){
                $(this).parent().parent().fadeOut(600).remove();
                reorderFCourse('#featured-course-list');
        });

        // ----- Create Linkbox Scripts
        // ----- Create Linkbox Scripts
        // ----- Create Linkbox Scripts
        // Function to Linkbox Slides (when remove)
        function reorderLinkbox( namelist ){
                var list =	$(namelist);
                var nLinkbox = '0';
                list.find("tr.new-linkbox").each(function(){
                    nLinkbox++;
                    //alert('data-number: '+$(this).attr('data-number')+' | nSlide: '+nSlide);
                    if ( ($(this).attr('data-number')) != nLinkbox ) {
                        // Change data-number of <tr>
                        $(this).attr('data-number', nLinkbox);
                        // Change data-number of <tr><td> > inputs and buttons
                        $(this).children('td').children('label').children('input.menu').attr('name','frontpage[linkboxdata]['+(nLinkbox-1)+'][title]');
                        $(this).children('td').children('label').children('input.link').attr('name','frontpage[linkboxdata]['+(nLinkbox-1)+'][link]');
                        $(this).children('td').children('label').children('select.icon').attr('name','frontpage[linkboxdata]['+(nLinkbox-1)+'][icon]');
                        $(this).children('td').children('label').children('textarea.desc').attr('name','frontpage[linkboxdata]['+(nLinkbox-1)+'][text]');
                        $(this).children('td').children('div.button').attr('data-remove','new-linkbox-'+(nLinkbox-1)+'');
                    }
                });
        }
        // Add a new linkbox text/link/icon
        $("#add-new-linkbox").click(function(){
                var html =	'';
                var list = $("#linkbox-list");
                var numLinkbox = '1';
                list.find("tr").each(function(){ numLinkbox++; });
                if (numLinkbox == '1') { fst = ' first'; } else { fst = ''; }
                html +=	'<tr class="new-linkbox'+fst+'" data-number="'+numLinkbox+'" style="display:none;">';
                html +=	'	<td><label class="left first">Title: ';
                html += '		<input type="text" class="menu" name="frontpage[linkboxdata]['+(numLinkbox-1)+'][title]" value="" />';
                html += '	</label><label class="left">Link (URL): ';
                html += '		<input type="text" class="link" name="frontpage[linkboxdata]['+(numLinkbox-1)+'][link]" value="http://" />';
                html += '	</label><label class="left">Image: ';
                html += '		<select class="icon" name="frontpage[linkboxdata]['+(numLinkbox-1)+'][icon]">';
                html += '			<option value="airplane">Airplane</option>';
                html += '			<option value="book">Book</option>';
                html += '			<option value="calendar">Calendar</option>';
                html += '			<option value="camera">Camera</option>';
                html += '			<option value="card">Card</option>';
                html += '			<option value="clock">Clock</option>';
                html += '			<option value="cloud">Cloud</option>';
                html += '			<option value="coffee">Coffee</option>';
                html += '			<option value="communication">Communication</option>';
                html += '			<option value="cross">Cross</option>';
                html += '			<option value="education">Education</option>';
                html += '			<option value="heart">Heart</option>';
                html += '			<option value="image">Image</option>';
                html += '			<option value="link">Link</option>';
                html += '			<option value="location">Location</option>';
                html += '			<option value="locked">Locked</option>';
                html += '			<option value="male">Male</option>';
                html += '			<option value="members">Members</option>';
                html += '			<option value="music">Music</option>';
                html += '			<option value="pen">Pen</option>';
                html += '			<option value="phone">Phone</option>';
                html += '			<option value="rate">Rate</option>';
                html += '			<option value="smartphone">Smartphone</option>';
                html += '			<option value="stats">Stats</option>';
                html += '			<option value="technology">Technology</option>';
                html += '			<option value="tick">Tick</option>';
                html += '			<option value="wrench">Wrench</option>';
                
                
                
                html += '		</select>';
                html += '	</label></td>';
                html += '	<td><label>Description: ';
                html += '		<textarea class="desc" name="frontpage[linkboxdata]['+(numLinkbox-1)+'][text]"></textarea>';
                html += '	</label><div class="button remove" data-remove="new-linkbox-'+(numLinkbox-1)+'">';
                html += '		Remove linkbox item';
                html += '	</div></td></tr>';
                list.append(html);
                list.find("tr").fadeIn(600);
        });
        // Remove a new linkbox
        $(".new-linkbox .remove").live('click', function(){
                $(this).parent().parent().fadeOut(600).remove();
                reorderLinkbox('#linkbox-list');
        });
});