class GoogleFirebase

{

    public $database;

    public $auth;

    public $factory;

    public function __construct()

    {

        $this->factory = (new Factory)

            ->withServiceAccount('../firebase_credentials.json')

            ->withDatabaseUri(config('services.firebase.database_url'));

 

        $this->database = $this->factory->createDatabase();

        $this->auth = $this->factory->createAuth();

    }

}
