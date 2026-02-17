<?php

return [
    'required' => 'Le champ :attribute est obligatoire.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'min' => [
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'numeric' => 'Le champ :attribute doit être supérieur ou égal à :min.',
    ],
    'max' => [
        'string' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
        'numeric' => 'Le champ :attribute ne peut pas dépasser :max.',
    ],
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'date' => 'Le champ :attribute doit être une date valide.',
    'in' => 'La valeur sélectionnée pour :attribute est invalide.',
    'exists' => 'La valeur sélectionnée pour :attribute est invalide.',
    'unique' => 'Cette valeur de :attribute est déjà utilisée.',
    'attributes' => [
        'name' => 'nom',
        'email' => 'email',
        'password' => 'mot de passe',
        'phone' => 'téléphone',
        'address' => 'adresse',
        'gender' => 'sexe',
    ],
];
