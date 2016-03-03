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
In previous example only columns actor_id (as table primary key), first_name and last_name will be automatically selected in consecutive calls.
