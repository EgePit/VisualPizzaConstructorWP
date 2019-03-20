<div class="vc-order <?php echo strtolower($this->status) ?>" id="order-<?php echo $this->id; ?>" data-order_id="<?php echo $this->id; ?>" data-pizzeria_id="<?php echo $this->pizzeria_id; ?>">
    <h3>ORDER: #<?php echo $this->id; ?> <?php echo strtolower($this->status) == 'offer' ? 'Waiting for response to offer' : '' ?></h3>
    <p>TID: <?php echo $this->date; ?></p>
    <?php foreach($this->main_products as $key=> $product): ?>
        <div class="main-product <?php echo $key > 0 ? 'collapsed' : '' ?>">
            <div class="product_thumb">
                <img src="<?php echo $product->image?>">
            </div>
            <div class="product-descr">
                <div class="product_ingradients">
                    <p>Consist</p>
                    <?php echo implode('<br/>', $product->ingradients); ?>
                </div>
                <span class="quantity">Quantity: <?php echo $product->quantity ?></span>
                <span class="price">Price: <?php echo $product->price * $product->quantity; ?><?php echo vconst_get_currency(); ?></span>
            </div>
        </div>
    <?php endforeach; ?>
    <?php foreach($this->side_products as $product): ?>
        <div class="side-product collapsed">
            <p><?php echo $product->title ?></p>
            <div class="product_thumb">
                <img src="<?php echo $product->image?>">
            </div>
            <div class="product-descr">
                <span class="quantity">Quantity: <?php echo $product->quantity ?></span>
                <span class="price">Price: <?php echo $product->price * $product->quantity; ?><?php echo vconst_get_currency(); ?></span>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="total-price collapsed">
        <p>Total price: <span class="total-price-val"><?php echo $this->total_price; ?></span><?php echo vconst_get_currency(); ?></p>
    </div>
    <div class="delivery collapsed">
        <p class="delivery_method">Delivery: <?php echo $this->delivery ?></p>
        <p class="delivery_when">Time: <span class="time-val"><?php echo $this->when ?></span></p>
    </div>
    <div class="status collapsed">
        <p>Status: <?php echo $this->status ?></p>
    </div>
    <?php if($this->status == ORDER_PENDING) : ?>
    <div class="edit-order collapsed">
        <button class="edit">Edit</button>
        <button class="save">Save</button>
    </div>
    <?php endif; ?>
    <a href="#" class="expand">[...]</a>
    <a href="#" class="collapse">[]</a>
    <?php if($this->status == ORDER_PENDING) : ?>
    <div class="actions">
        <button class="accept">Accept</button>
        <button class="deny">Deny</button>
    </div>
    <?php endif; ?>
    <?php if($this->status == ORDER_ACCEPTED) : ?>
        <div class="actions">
            <button class="done">Done</button>
        </div>
    <?php endif; ?>
</div>
