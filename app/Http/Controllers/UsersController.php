<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\Maggiorenne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    //Registrazione utente

    public function register(Request $request)
    {
        try{
           

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                'birthdate' =>  ['required','date', new Maggiorenne]
            ], [
                'name.required'=>'Il nome è richiesto.',
                'surname.required'=>'Il cognome è richiesto.',
                'email.email'=>'E richiesta una mail ',
                'email.unique'=>'Email già presente',
                'password.required' => 'La password è obbligatoria.',
                'password.min' => 'La password deve essere lunga almeno 8 caratteri.',
                'password' => 'La password deve contenere almeno un carattere speciale (@,$,!,%,*,?,&), un numero, una maiuscola e una minuscola.',
                
            ]);
            
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }
    
           
    
            $user=User::create([
                "name"=>$request->name,
                "surname"=>$request->surname,
                "email"=>$request->email,
                "password"=>Hash::make($request->password),
                "birthdate"=>$request->birthdate
            ]);
            return response()->json([ 
            'status' => true,
            'message' => 'Utente registrato con successo'], 201);
        } catch (\Exception $e) {
            // In caso di erroree
            return response()->json([
                'message' => 'Utente non registrato correttamente', 
                'error' => $e->getMessage()
            ], 404);
        }
        
    }

    //Vista dati profilo

    public function profile () {
    
    try{
        $data = Auth::user();
        return response()->json([
            'status'=> true,
            'data'=> $data
        ]);
    } catch (\Exception $e) {
        // In caso di erroree
        return response()->json([
            'message' => 'Utente non loggato', 
            'error' => $e->getMessage()
        ], 404);
    }
        
    }

    //modifica utente
    public function update(Request $request, $id)
    {

        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'email' => 'required|string|email',
                'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                'birthdate' =>  ['required','date', new Maggiorenne]
            ], [
                'name.required'=>'Il nome è richiesto.',
                'surname.required'=>'Il cognome è richiesto.',
                'email.email'=>'E richiesta una mail ',
                'email.unique'=>'Email già presente',
                'password.required' => 'La password è obbligatoria.',
                'password.min' => 'La password deve essere lunga almeno 8 caratteri.',
                'password' => 'La password deve contenere almeno un carattere speciale (@,$,!,%,*,?,&), un numero, una maiuscola e una minuscola.',
                
            ]);
            
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $user->update([
                'name'=>$request->name,
                'surname'=>$request->surname,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
                'birthdate'=>$request->birthdate
            ]);
            return response()->json([
                'status' => true,
                'message'=> 'Utente modificato'
            ]);
        } catch (\Exception $e) {
            // In caso di erroree
            return response()->json([
                'message' => 'Utente non trovato', 
                'error' => $e->getMessage()
            ], 404);
        }
    }

    //eliminazione utente

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'Utente eliminato con successo']);
        } catch (\Exception $e) {
            // In caso di erroree
            return response()->json(['message' => 'Utente non trovato',
             'error' => $e->getMessage()], 404);
        };

    }


}
