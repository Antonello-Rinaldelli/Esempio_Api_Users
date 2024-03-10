<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    //login utente
    public function login(Request $request)
    {

        try{
        $request->validate([
            'email'=> 'required|email',
            'password'=> 'required'
        ]);

        if (Auth::attempt([
            'email'=>$request->email,
            'password'=>$request->password
        ])) {
            // Genera e invia l'OTP via email
            $user = Auth::user();

            $otp = rand(100000, 999999); 
                $user->otp = $otp;
                $user->save();

            Mail::raw("Il tuo codice OTP è: $otp", function ($message) use ($user) {
                $message->to($user->email)->subject('Codice OTP per il login');
                }); 

            


            return response()->json([
                'status' => true,
                'message' => 'Utente trovato, otp inviato via email',
                
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Credenziali inesistenti'], 401);
        }} catch (\Exception $e) {
            // In caso di erroree
            return response()->json([
                'message' => 'Api momentaneamente non disponibile',
                'error' => $e->getMessage()], 404);
        };
    }

    //inserimento otp e token autenticazione

    public function otp (Request $request) {
        try {
        $request->validate([
            'email'=> 'required|email',
            'otp'=> 'required|min:6'
        ]);
    
            //$user = User::findOrFail($request);
            $user = User::where('email', $request->email)->first();
            if ($user && $request->otp == $user->otp) {
    
                $token = $user->createToken('AccessToken')->plainTextToken;
                return response()->json([
                    'state'=> true,
                    'token' => $token
                ]);
            } else {
                return response()->json(['error' => 'OTP non valido.'], 401);
            }
        } catch (\Exception $e) {
            // In caso di erroree
            return response()->json([
                'message' => 'Api momentaneamente non disponibile', 
                'error' => $e->getMessage()], 404);
        };
    
    
    }

    //Logout utente

    public function logout () 
    {

        try{
        Auth::user()->tokens()->delete();
    
        return response()->json([
            'status'=> true,
            'message'=> 'Logout effettuato correttamente'
        ]);
        } catch (\Exception $e) {
            // In caso di erroree
            return response()->json([
                'message' => 'Utente non loggato', 
                'error' => $e->getMessage()], 404);
        };
    
    }
}
