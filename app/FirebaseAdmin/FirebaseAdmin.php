use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseAdmin
{
protected $firebase;

public function __construct()
{
$serviceAccount = ServiceAccount::fromJsonFile(storage_path('/serviceAccount.json'));

$this->firebase = (new Factory)
->withServiceAccount($serviceAccount)
->create();
}
}