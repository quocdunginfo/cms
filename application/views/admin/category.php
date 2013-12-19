<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$this->load->view('admin/header');
?>

            <!-- module goes here -->
			<!-- Category -->
            <div class="grid_6">
                <!-- Notification boxes -->
                
                <div class="module">
                     <h2><span>Category function</span></h2>

                     <div class="module-body">
                        <a name="cat_add"></a>
                        <script>
                        function qd_choose()
                        {
                            var rates = document.getElementsByName('cat_radio_list[]');
                            var rate_value;
                            for(var i = 0; i < rates.length; i++){
                                if(rates[i].checked){
                                    rate_value = rates[i].value;
                                    //call parent function
                                    window.opener.qd_menu_param(rate_value);
                                    window.close();
                                    return false;
                                }
                            }
                            
                            return false;
                        }
                        
                        </script>
                        <span class="notification n-success" <?php if(!in_array('add_ok',$state)) echo 'style="display:none;"'; ?>>Added successfully!</span>
                        <?php if($view_mode=='selector') { ?>
                        <input class="submit-green" type="button" value="Choose" onclick="return qd_choose()" />
                        
                        <?php } else { ?>
                        <p>
                        Choose parent category:
                        </p>
                        <?php } ?>
                        <form action="<?php echo site_url('admin_category/add/special/'.$special.'/view_mode/'.$view_mode); ?>" method="post">
                            <fieldset>
                                <ul class="qdClass" style="border:2px solid #ccc; width:80%px; height: 350px; overflow-y: scroll; padding:10px 10px 10px 10px;">
                                    <li>
                                        <label>
                                            <input checked="checked" type="radio" name="cat_radio_list[]" value="-1"/>
                                            [Root category]
                                        </label>
                                    </li>
                                    <?php
                                    
                                    foreach($cat_list as $cat_obj):
                                
                                    ?>
                                    
                                    <li>
                                        <label>
                                            
                                            <input id="cat_id_<?=$cat_obj->id?>" style="margin-left:<?php echo ($cat_obj->level+1)*20; ?>px;" type="radio" name="cat_radio_list[]" value="<?php echo $cat_obj->id; ?>"/>
                                            &nbsp;
                                            <span id="cat_name_<?=$cat_obj->id?>"><?php echo $cat_obj->name; ?></span>
                                            <a onclick="return confirm_click();" href="<?php echo site_url('admin_category/delete/'.$cat_obj->id.'/'.$special); ?>" style="float: right;">Delete</a>
                                            
                                            <a href="javascript:transfer_cat_edit(<?=$cat_obj->id?>);" style="float: right; margin-right: 20px;" >Edit</a>
                                            
                                        </label>
                                    </li>
                                    <?php
                                    endforeach;
                                    ?>
                                    <script language="javascript">
                                        function confirm_click() {
                                            if (confirm("Are you sure to do this task ?")) {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        }
                                        function transfer_cat_edit(cat_id)
                                        {
                                            var new_name = $('#cat_name_'+cat_id).text();
                                            $('#cat_edit_id').attr("value",cat_id);
                                            $('#cat_edit_name').attr("value",new_name);
                                            //auto select radio
                                            $('#cat_id_'+cat_id).attr('checked','checked');
                                            //move to edit area
                                            window.location.hash="cat_edit";
                                            //notification for add
                                            //auto focus
                                            $("#cat_edit_name").focus();
                                            qd_blink("#qd_update");
                                        }
                                        //notification for add
                                        $( document ).ready(function() {
                                            //auto focus
                                            $("#cat_add_name").focus();
                                            qd_blink("#qd_add");
                                        });
                                    </script>
                                </ul>
                            </fieldset>
                            
                            <fieldset id="qd_add">
                                Category name:
                                <input id="cat_add_name" name="cat_name" type="text" class="input-short" style="width: 200px;"/>
                                &nbsp;
                                <input class="submit-green" type="submit" value="Add category" />
                                <a name="cat_add"></a>
                            </fieldset>
                        </form>

                        <!-- <a href="<?php echo site_url('admin_media/validate'); ?>"><input class="submit-green" value="Validate" style="width:100px; text-align:center;" onclick="javascript:cat_item_click(this)"></a> -->
                     </div> <!-- End .module-body -->
                </div> <!-- End .module -->
                <div style="clear:both;"></div>
            </div> <!-- End .grid_6 -->
            
            <!-- Category edit panel -->
            <div class="grid_6" id="qd_update">
                <!-- Notification boxes -->
                
                <div class="module">
                     <h2><span>Category function</span></h2>
                     <div class="module-body">
                        <span class="notification n-success" <?php if(!in_array('edit_ok',$state)) echo 'style="display:none;"'; ?>>Updated successfully!</span>
                        <p>Edit category</p>  
                        <form action="<?php echo site_url('admin_category/edit/special/'.$special.'/view_mode/'.$view_mode); ?>" method="post">
                            <fieldset>
                                Category name:
                                <input id="cat_edit_id" name="cat_id" type="hidden" value=""/>
                                <input id="cat_edit_name" value="" name="cat_name" type="text" class="input-short" style="width: 200px;"/>
                                &nbsp;
                                <input class="submit-green" type="submit" value="Submit" />
                                <a name="cat_edit"></a>
                            </fieldset>
                        </form>

                        
                     </div> <!-- End .module-body -->
                </div> <!-- End .module -->
                <div style="clear:both;"></div>
            </div> <!-- End .grid_6 -->
            <div style="clear:both;"></div>
<?php
$this->load->view('admin/footer');
?>