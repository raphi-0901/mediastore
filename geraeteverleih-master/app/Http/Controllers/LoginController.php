<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function redirectToProvider()
    {
        return Socialite::driver('azure')->stateless()->redirect();
    }

    public function logout()
    {
        $user = User::all()->find(Auth::id());
        Auth::logout();

        //den User nur von Microsoft ausloggen, wenn er auch wirklich einen verbundenen Account hat.
        if(!$user || $user->microsoftId)
            $url = 'https://login.microsoftonline.com/' . env('AZURE_TENANT_ID') . '/oauth2/logout?post_logout_redirect_uri=' . urlencode(route('index'));
        else
            $url = route('index');
        return Redirect::to($url);
    }

    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('azure')->stateless()->user();
            if ($user) {
                $authUser = $this->findOrCreateUser($user);

                //wenn der Benutzer gelöscht wurde, wird er nicht mehr zugelassen
                if($authUser->trashed())
                    return redirect(route("login"))->withErrors('Dein Konto wurde gesperrt!');

                Auth::login($authUser);
                return redirect()->intended(RouteServiceProvider::HOME);
            }
        } catch (ClientException $ex) {
            return redirect(route("login"))->withErrors("Login fehlgeschlagen.");
        }
    }

    private function findOrCreateUser($user)
    {
        //überprüfen, ob es den Benutzer bereits gibt.
        $nUser = User::withTrashed()
            ->where('email', $user->email)
            ->orWhere('email', $user->user["mail"])
            ->orWhere('microsoftId', $user->getId())
            ->first();

        //wenn er noch nicht existiert, wird er erstellt.
        if (!$nUser)
            $nUser = new User();

        //falls er schon existierte, werden die Werte überschrieben. (falls neue Klasse oder neuer Name)
        $nUser->email = strtolower($user->email);
        $nUser->lastName = strtoupper($user->user["surname"]);
        $nUser->firstName = $user->user["givenName"];

        //Beispiel Anzeigename: Wirnsberger Raphael, 4AHITM
        //wir holen uns hier die Klasse
        $class = explode(",", $user->name)[1];

        //nur Schüler haben hinter ihrem Namen eine Zahl für die Klasse. Die Lehrer haben Prof., DI. oder sonstiges stehen.
        if (1 === preg_match('~[0-9]~', $class))
        {
            $nUser->role_id = 3;
            $nUser->class = $class;
        }
        else
        {
            $nUser->role_id = 2;
            $nUser->class = 'Lehrer';
        }

        $nUser->microsoftId = $user->getId();
        $nUser->save();

        return $nUser;
    }
}
