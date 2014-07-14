<?php echo $header; ?>

<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">    
    <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-general" class="page">
          <table class="form">
            
            <tr>
              <td width=""><?php echo $entry_status; ?></td>
              <td>
                <select name="opay_status">
                  <?php if ($opay_status): ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php else: ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php endif; ?>
                </select>
              </td>
            </tr>

            <tr>
              <td><span class="required">*</span> <?php echo $entry_website; ?></td>
              <td>
                <input type="text" name="opay_website_id" value="<?php echo $opay_website_id; ?>" size="" /> <br>
                <?php if ($error_opay_website_id): ?>
                  <span class="error"><?php echo $error_opay_website_id; ?></span>
                <?php endif; ?>
              </td>
            </tr>

            <tr>
              <td><?php echo $entry_user_id; ?></td>
              <td>
                <input type="text" name="opay_user_id" value="<?php echo $opay_user_id; ?>" size="" />
                <?php if ($error_opay_user_id): ?>
                  <span class="error"><?php echo $error_opay_user_id; ?></span>
                <?php endif; ?>
              </td>
            </tr>

            <tr>
              <td><?php echo $entry_test_mode; ?></td>
              <td>
                <select name="opay_test_mode">
                  <?php if ($opay_test_mode): ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                  <?php else: ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php endif; ?>
                </select>
              </td>
            </tr>

            <tr>
              <td><span class="required">*</span> <?php echo $entry_password_signature; ?></td>
              <td>
                <input type="text" name="opay_password_sign" value="<?php echo $opay_password_sign; ?>" size="38" />
                <?php if ($error_opay_password_sign): ?>
                  <span class="error"><?php echo $error_opay_password_sign; ?></span>
                <?php endif; ?>
              </td>
            </tr>

            <tr>
              <td><span class="required">*</span> <?php echo $entry_rsa_signature; ?></td>
              <td>
                <textarea name="opay_rsa_signature" id="" cols="120" rows="10"><?php echo $opay_rsa_signature; ?></textarea>
                <?php if ($error_opay_rsa_signature): ?>
                  <span class="error"><?php echo $error_opay_rsa_signature; ?></span>
                <?php endif; ?>
              </td>
            </tr>

            <tr>
              <td><span class="required">*</span> <?php echo $entry_certificate; ?></td>
              <td>
                <textarea name="opay_certificate" id="" cols="120" rows="10"><?php echo $opay_certificate; ?></textarea>
                <?php if ($error_opay_certificate): ?>
                  <span class="error"><?php echo $error_opay_certificate; ?></span>
                <?php endif; ?>
              </td>
            </tr>

            <tr>
              <td><?php echo $entry_new_order_status; ?></td>
              <td>
                <select name="opay_new_order_id">
                  <?php foreach ($order_statuses as $order_status): ?>
                      <?php if ($order_status['order_status_id'] == $opay_new_order_id): ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php else: ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
                </select>
              </td>
            </tr>

            <tr>
              <td><?php echo $entry_finished_order_status; ?></td>
              <td>
                <select name="opay_finished_order_id">
                  <?php foreach ($order_statuses as $order_status): ?>
                      <?php if ($order_status['order_status_id'] == $opay_finished_order_id): ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php else: ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
                </select>
              </td>
            </tr>

            <tr>
              <td><?php echo $entry_canceled_order_status; ?></td>
              <td>
                <select name="opay_canceled_order_id">
                  <?php foreach ($order_statuses as $order_status): ?>
                      <?php if ($order_status['order_status_id'] == $opay_canceled_order_id): ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php else: ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
                </select>
              </td>
            </tr>

            <tr>
              <td width=""><?php echo $entry_show_channels; ?></td>
              <td>
                <select name="opay_show_channels">
                <?php if ($opay_show_channels): ?>
                  <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                  <option value="0"><?php echo $text_no; ?></option>
                <?php else: ?>
                <option value="1"><?php echo $text_yes; ?></option>
                <option value="0" selected="selected"><?php echo $text_no; ?></option>
                <?php endif; ?>
                </select>
              </td>
            </tr>

            <tr>
              <td><?php echo $entry_geo_zone; ?></td>
              <td>
                  <select name="opay_geo_zone_id">
                    <option value="0"><?php echo $text_all_zones; ?></option>
                    <?php foreach ($geo_zones as $geo_zone): ?>
                        <?php if ($geo_zone['geo_zone_id'] == $opay_geo_zone_id): ?>
                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                            <?php else: ?>
                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                  </select>
              </td>
            </tr>

            <tr>
              <td><?php echo $entry_sort_order; ?></td>
              <td>
                <input type="text" name="opay_sort_order"value="<?php echo $opay_sort_order; ?>" size="1" />
              </td>
            </tr>
           
          </table>
        </div>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?> 