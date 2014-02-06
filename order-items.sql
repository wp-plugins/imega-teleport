select p.id, p.post_date, i.order_item_name, im.meta_key, im.meta_value, tp.guid from {$table_prefix}posts p
	left join {$table_prefix}woocommerce_order_items i on i.order_id = p.id
	left join {$table_prefix}woocommerce_order_itemmeta im on im.order_item_id = i.order_item_id
	left join {$table_prefix}imegateleport tp on tp.object_id = im.meta_value and im.meta_key = '_product_id'
	where p.post_type = 'shop_order'
	and im.meta_key = '_qty'
	or im.meta_key = '_product_id'
	or im.meta_key = '_line_total';