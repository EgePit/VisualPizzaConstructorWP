<h3>Main menu</h3>
<?php foreach($this->full_main_menu as $group=> $list) : ?>
<h3><?php echo ucfirst($group) ?></h3>
<table>
    <thead>
    <tr>
        <th>Parametr</th>
        <th>On</th>
        <th>Off</th>
        <th>Price</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($list['options'] as $item) : ?>
        <tr>
            <td><span><?php echo $item['title'] ?></span></td>
            <td><input type="radio" name="menu[<?php echo $item['title'] ?>][status]" <?php echo $this->settings['menu'][$item['title']]['status'] == 'true' ? 'checked': '' ?> value="true"></td>
            <td><input type="radio" name="menu[<?php echo $item['title'] ?>][status]" <?php echo $this->settings['menu'][$item['title']]['status'] == 'false' ? 'checked': '' ?> value="false"></td>
            <td><input type="number" class="price" name="menu[<?php echo $item['title'] ?>][price]" value="<?php echo $this->settings['menu'][$item['title']]['price'] ?>"/><?php echo vconst_get_currency(); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endforeach; ?>

<h3>Side menu</h3>
<table>
    <thead>
    <tr>
        <th>Parametr</th>
        <th>On</th>
        <th>Off</th>
        <th>Price</th>
    </tr>
    </thead>
    <tbody>
<?php foreach($this->full_side_menu as $item) : ?>
    <tr>
        <td><span><?php echo $item['title'] ?></span></td>
        <td><input type="radio" name="menu[<?php echo $item['title'] ?>][status]" <?php echo $this->settings['menu'][$item['title']]['status'] == 'true' ? 'checked': '' ?> value="true"></td>
        <td><input type="radio" name="menu[<?php echo $item['title'] ?>][status]" <?php echo $this->settings['menu'][$item['title']]['status'] == 'false' ? 'checked': '' ?> value="false"></td>
        <td><input type="number" class="price" name="menu[<?php echo $item['title'] ?>][price]" value="<?php echo $this->settings['menu'][$item['title']]['price'] ?>"/><?php echo vconst_get_currency(); ?></td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<h3>Payments</h3>
<table>
    <thead>
    <tr>
        <th>Parametr</th>
        <th>On</th>
        <th>Off</th>
    </tr>
    </thead>
    <tbody>
<?php foreach($this->payments as $gateway_name=> $item) : ?>
    <tr>
        <td><span><?php echo $item ?></span></td>
        <td><input type="radio" name="payments[<?php echo $gateway_name ?>]" <?php echo $this->settings['payments'][$gateway_name] == 'true' ? 'checked': '' ?> value="true"></td>
        <td><input type="radio" name="payments[<?php echo $gateway_name ?>]" <?php echo $this->settings['payments'][$gateway_name] == 'false' ? 'checked': '' ?> value="false"></td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>