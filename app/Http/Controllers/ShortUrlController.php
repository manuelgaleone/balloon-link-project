<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\ShortUrl;


class ShortUrlController extends Controller
{
    public function store(Request $request)
    {
        // Validazione dell'input dell'utente
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return redirect('/')
                ->withErrors($validator)
                ->withInput();
        }

        // Generazione dell'URL accorciato
        $shortUrl = Str::random(6);

        // Salvataggio dell'URL originale e dell'URL accorciato nel database
        $shortUrlModel = new ShortUrl();
        $shortUrlModel->original_url = $request->input('url');
        $shortUrlModel->short_url = $shortUrl;
        $shortUrlModel->save();

        // Restituzione della vista con l'URL accorciato
        return view('shorten.result', [
            'shortUrl' => $shortUrl,
        ]);
    }

    public function show($shortUrl)
    {
        // Cerca l'URL originale corrispondente all'URL accorciato nel database
        $shortUrlModel = ShortUrl::where('short_url', $shortUrl)->first();

        // Se l'URL accorciato non corrisponde a un URL originale, restituisci un errore 404
        if (!$shortUrlModel) {
            abort(404);
        }

        // Reindirizza l'utente all'URL originale
        return redirect($shortUrlModel->original_url);
    }

    public function index()
    {
        // Recupera tutti gli URL accorciati dal database
        $shortUrls = ShortUrl::all();

        // Restituisce la vista con l'elenco degli URL accorciati
        return view('shorten.index', [
            'shortUrls' => $shortUrls,
        ]);
    }
}
