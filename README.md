# Query-Builder
Easier querying in PHP to MySQL.

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
	
	//Columns to be selected in the DB
	$QB->select('customers', $select); 
	
	//Join fields
	$QB->join('company', 'company.id = customers.company_id'); 
	
	//Where field and value set in array, can be multiple.
	$QB->where($where); 
	
	//Encrypt id column on fetch.
	$QB->encryptColumn('id'); 
	
	//Get all rows.
	$customers = $QB->getAll(); 

	$QB->select('company'); 
	$QB->encryptColumn('id');
	$companies = $QB->getAll();
