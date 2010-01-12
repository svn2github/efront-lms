        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=social">'|cat:$smarty.const._SOCIAL|cat:'</a>'}
        {*moduleModules: The social administration page*}
        {capture name = "moduleSocialAdmin"}
                    <tr><td class="moduleCell">
                        {literal}
                        <script>
                        function activate(el, moduleId) {
                            Element.extend(el);
                            var src = Element.down(el).src;
                            src.match(/_gray/) ? url = '{/literal}{$smarty.server.PHP_SELF}{literal}?{/literal}ctg=social&{literal}&ajax=1&activate='+moduleId : url = '{/literal}{$smarty.server.PHP_SELF}{literal}?{/literal}ctg=social&{literal}&ajax=1&deactivate='+moduleId;
                            Element.down(el).blur();
                            Element.down(el).setAttribute('src', 'images/others/progress_big.gif');
                            new Ajax.Request(url, {
                                    method:'get',
                                    asynchronous:true,
                                    onSuccess: function (transport) {
                                        if (transport.responseText.lastIndexOf("!") > 0) {
                                            response = transport.responseText.substr(0,1);
                                            force_sidebar_reload = 1;
                                        } else {
                                            response = transport.responseText;
                                            force_sidebar_reload = 0;
                                        }

                                        if (response != "1") {
                                            table = $('Options+for+eFront+social_tableId');
                                            var els = table.down().getElementsByTagName("td");
                                            var elementsArray = new Array();
                                            for (var i = 0; i < els.length; i++) {
                                                if(els[i].className == "iconTableTD") {
                                                    elementsArray.push(els[i]);
                                                }
                                            }

                                            //alert(transport.responseText);

                                            if (response == "2") {
                                                    //// to let JS know to change the display of the "all options" icon to activated

                                                    Element.extend(elementsArray[0]);
                                                    elementsArray[0].down().down().setAttribute('src', src.replace(/_gray/, ''));
                                                    elementsArray[0].down().setStyle({color:'black'});
                                            } else if (response == "3") {
                                                    //// to let JS know to change the display of the "all options" icon to be deactivated
                                                    Element.extend(elementsArray[0]);
                                                    Element.down(elementsArray[0].down()).setAttribute('src', src.replace(/.png/, '_gray.png'));
                                                    elementsArray[0].down().setStyle({color:'gray'});

                                            } else if (response == "4") {
                                                    //// to let JS know to change display of all icons to activated
                                                    for (i = 0; i < elementsArray.length; i++) {
                                                        Element.extend(elementsArray[i]);
                                                        Element.down(elementsArray[i].down()).setAttribute('src', src.replace(/_gray/, ''));
                                                        elementsArray[i].down().setStyle({color:'black'});
                                                    }
                                            } else if (response == "5") {
                                                    //// to let JS know to change display of all icons to deactivated
                                                    for (i = 0; i < elementsArray.length; i++) {
                                                        Element.extend(elementsArray[i]);
                                                        Element.down(elementsArray[i].down()).setAttribute('src', src.replace(/.png/, '_gray.png'));
                                                        elementsArray[i].down().setStyle({color:'gray'});
                                                    }
                                            }

                                        }

                                        if (src.match(/_gray/)) {
                                            Element.down(el).setAttribute('src', src.replace(/_gray/, ''));
                                            el.setStyle({color:'black'});
                                        } else {
                                            Element.down(el).setAttribute('src', src.replace(/.png/, '_gray.png'));
                                            el.setStyle({color:'gray'});
                                        }

                                        // Only a few options trigger sidebar reload for the administrator
                                        if (force_sidebar_reload) {
                                            parent.sideframe.location = parent.sideframe.location + '?sbctg=control_panel';
                                        }

                                    }
                                });
                        }

                        </script>
                        {/literal}

                        {eF_template_printIconTable title = $smarty.const._OPTIONSFORSOCIALMODULE columns = 4 links = $T_SOCIAL_SETTINGS image='32x32/environment.png'}

                    </td></tr>
        {/capture}