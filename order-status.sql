/*
 * обновление статуса заказа
 */
update {$table_prefix}term_relationships set term_taxonomy_id=(
	select tt.term_taxonomy_id from {$table_prefix}term_taxonomy tt
		left join {$table_prefix}terms t on t.term_id = tt.term_id
		where t.slug = '{$status}')
		where object_id = {$id};