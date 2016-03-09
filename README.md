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

It's also possible to get one row from table defined by it's id.

	$row = $db->film[$id]

To get referenced table, simply get table name from row. Library uses `Structure\Convention->foreignKey()` as source to match
referenced table primary key identified by `Structure\Convention->primaryKey()`.

	$row->language

To obtain referencing column value instead of referenced table, use column name.

	$row['language_id']

To work with Row as an array use \ArrayAccess, \IteratorAggregate or `toArray` method.

	$row['name']; // Array access to column name
	foreach ($row->getIterator() as $columnName => $value) { // Iterate over all columns
		$columnName . ': ' . $value;
	}
	$row->toArray(); // Get all columns as array

Get corresponding rows from referenced table found by specified column. Method `Table->source()` specifies which column use from first table.
To specify which column match method `Table->references()` shall be used.

	foreach ($db->directory as $directory) {
		$parentDirectory = $directory->directory->source('parent_id');
		$subDirectories = $directory->directory->references('parent_id');
	}

Both methods accepts multiple arguments to allow table referencing by compound key.

	$row->target->source('lang_id', 'target_id')->references('lang', 'id');
