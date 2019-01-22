# Query-Builder

Sample usage:

*$DB is the PDO Connection.

	$QB = new QueryBuilder($DB); //Instantiate the Query Builder and pass the connection to the constructor.

	$select = array(
		'`customers`.`id`',
		'`customers`.`name`',
		'`customers`.`date_added`',
		'`company`.`name` AS company'
	);

	$where = array(
		'status' => 'active'
	);

	$QB->select('customers', $select); //Columns to be selected in the DB
	$QB->join('company', 'company.id = customers.company_id'); //Join fields
	$QB->where($where); //Where field and value set in array, can be multiple.
	$QB->encryptColumn('id'); //Encrypt id column on fetch.
	$customers = $QB->getAll(); //Get all rows.

	$QB->select('company'); 
	$QB->encryptColumn('id');
	$companies = $QB->getAll();
