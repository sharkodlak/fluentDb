# FluentDB

Fluent DB provides high performance DB layer with easy to use fluent interface.
Inspired by NotORM and DibiFluent.

Use table film:

	$db->film

Select all columns and all rows from table language:

	$db->language->getQuery()->getResult()->fetchAll()

Select all rows from table actor, but let library to select only necessary columns:

	foreach ($db->actor as $actor) {
		$actorName = $actor['first_name'] . ' ' . $actor['last_name'];
	}

In previous example only columns actor_id (as table primary key), first_name and last_name will be automatically selected
in consecutive calls (in case of cache hit).

To get referenced table row, simply get table name from row. Library uses `Structure\Convention->foreignKey()` as source to match
referenced table primary key identified by `Structure\Convention->primaryKey()`.

	$row->language

To obtain referencing column value instead of referenced table, use column name.

	$row['language_id']

Get corresponding rows from referenced table found by specified column. Method `Table->via()` specifies which column use from source table
and optionaly from referenced table. This method returns one row.
To find which rows references this value, method `Table->backwards()` shall be used. Method accepts optional arguments to specify
column name from second table (defauts to foreign key referencing first table) and which column name to match in first table
(defaults to primary key) - so these arguments are in opposite order against arguments from `Table->via()`. This method
returns collection of corresponding rows.

	foreach ($db->directory as $directory) {
		$parentDirectory = $directory->directory->via('parent_id');
		$subDirectories = $directory->directory->backwards('parent_id');
	}

Row acts as an array, it can be iterated and method `toArray` returns all columns and values.

	$row['name']; // Array access to column named 'name'
	foreach ($row as $columnName => $value) { // Iterate over all columns
		$columnName . ': ' . $value;
	}
	$row->toArray(); // Get all columns as array


To be implemented later...
--------------------------

It's possible to get referencing column value without knowledge of column name, the table name is required instead. Due to lazy loading
it has no performance impact. Select from referenced table will performed only if another than primary key column will be used.
This feature works only if DB has consistent Referential Integrity, because it mimics matching row in referenced table.

	// Typical use is $row['language_id']
	$row->language[':id']

It's also possible to get one row from table defined by it's id.

	$row = $db->film[$id]

Both methods accepts multiple arguments to allow table referencing by compound key.

	$row->target->source('lang_id', 'target_id')->backwards('lang', 'id');
