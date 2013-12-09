<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$this->load->view('front/header');
?>
<div id="content" class="float_r">
    <h2>Giỏ hàng</h2>
    <table width="680px" cellspacing="0" cellpadding="5">
        <tbody>
            <tr bgcolor="#ddd">
                <th width="70" align="center">Hình ảnh</th>
                <th align="center">Tên sản phẩm</th>
                <th width="60" align="center">Số lượng</th>
                <th width="90" align="right">Đơn giá</th>
                <th width="110" align="right">Tổng cộng</th>
                <th width="20"></th>
            </tr>
            <tr>
                <td colspan="7">
                    
                </td>
            </tr>
            <?php foreach($giohang->get_order_detail_list() as $item) { ?>
            <tr>
                <td>
                    <a href="">
                    <img src="<?=$item->get_product_obj()->get_avatar_thumb()?>" alt="image 3" style="max-width:70px; max-height:70px;">
                    </a>
                </td>
                <td align="center">
                    <a href="<?=site_url('front/product/index/'.$item->get_product_obj()->id)?>">
                    <?=$item->get_product_obj()->title?>
                        </a>
                </td>
                
                <td align="center">
                    <form id="qd_form_0" action="/tmdtud/FrontCart/Add_Or_Update" method="post">
                        <input type="hidden" value="132" name="painting_id">
                        <input type="hidden" value="1" name="update_from_cart">
                      <label class="mylabel">
                      <select name="count" style="width: 40px;" onchange="submit()">
                            <option value="1" <?php if($item->order_count==1) echo 'selected="selected"'; ?>>1</option>
                            <option value="2" <?php if($item->order_count==2) echo 'selected="selected"'; ?>>2</option>
                            <option value="3" <?php if($item->order_count==3) echo 'selected="selected"'; ?>>3</option>
                        </select></label>
                        
                    </form>
                </td>
                <td align="right"><?=$item->get_order_unit_price()?> VNĐ</td>
                <td align="right"><?=$item->get_total_string()?> VNĐ</td>
                <td align="center">
                    <a href="javascript:qd_confirm( '/tmdtud/FrontCart/Remove?chitietsp_id=132' )">
                    <img src="/tmdtud/Content/front/images/remove_x.gif" alt="remove" style="width: 15px; height: 15px" title="Xóa khỏi giỏ hàng"></a>

                </td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="6" style="color:red;">
                </td>
            </tr>
            <tr>
                <td colspan="3" align="right" height="30px"></td>
                <td align="right" style="font-weight: bold">Tổng tiền:</td>
                <td align="right" style="font-weight: bold; font-size:12px"><?=$giohang->get_order_total_unsave_string()?> VNĐ</td>
                <td style=""></td>
            </tr>
        </tbody>
    </table>
    <div style="float: right;margin-right:10px; margin-top: 20px;">

        <p><a href="/tmdtud/FrontCart/CheckOut" class="mybutton" style="color:white;height:20px;width:120px;font-size:12px;font-weight:bold">Thanh toán</a></p></div>
     <div style="float: left; margin-top: 20px;">
        <p><a href="/tmdtud/" class="mybutton" style="color:white;height:20px;width:120px;font-size:12px;font-weight:bold">Tiếp tục mua hàng</a></p>
         </div>
 <div style="clear:both"></div>
</div>
<?php
$this->load->view('front/footer');
?>