<?php



return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'accepted' => 'El campo :attribute debe ser aceptado.',
    'accepted_if' => 'El campo :attribute debe ser aceptado cuando :other es :value.',
    'active_url' => 'El campo :attribute debe ser una URL válida.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => 'El campo :attribute solo debe contener letras.',
    'alpha_dash' => 'El campo :attribute solo debe contener letras, números, guiones y guiones bajos.',
    'alpha_num' => 'El campo :attribute solo debe contener letras y números.',
    'any_of' => 'El campo :attribute no es válido.',
    'array' => 'El campo :attribute debe ser un arreglo.',
    'ascii' => 'El campo :attribute solo debe contener caracteres alfanuméricos y símbolos de un byte.',
    'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'array' => 'El campo :attribute debe tener entre :min y :max elementos.',
        'file' => 'El campo :attribute debe pesar entre :min y :max kilobytes.',
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'can' => 'El campo :attribute contiene un valor no autorizado.',
    'confirmed' => 'La confirmación del campo :attribute no coincide.',
    'contains' => 'Al campo :attribute le falta un valor obligatorio.',
    'current_password' => 'La contraseña es incorrecta.',
    'date' => 'El campo :attribute debe ser una fecha válida.',
    'date_equals' => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format' => 'El campo :attribute debe coincidir con el formato :format.',
    'decimal' => 'El campo :attribute debe tener :decimal decimales.',
    'declined' => 'El campo :attribute debe ser rechazado.',
    'declined_if' => 'El campo :attribute debe ser rechazado cuando :other es :value.',
    'different' => 'El campo :attribute y :other deben ser diferentes.',
    'digits' => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between' => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'dimensions' => 'El campo :attribute tiene dimensiones de imagen no válidas.',
    'distinct' => 'El campo :attribute tiene un valor duplicado.',
    'doesnt_contain' => 'El campo :attribute no debe contener ninguno de los siguientes valores: :values.',
    'doesnt_end_with' => 'El campo :attribute no debe terminar con ninguno de los siguientes valores: :values.',
    'doesnt_start_with' => 'El campo :attribute no debe comenzar con ninguno de los siguientes valores: :values.',
    'email' => 'El campo :attribute debe ser un correo electrónico válido.',
    'encoding' => 'El campo :attribute debe estar codificado en :encoding.',
    'ends_with' => 'El campo :attribute debe terminar con uno de los siguientes valores: :values.',
    'enum' => 'El valor seleccionado en :attribute no es válido.',
    'exists' => 'El valor seleccionado en :attribute no es válido.',
    'extensions' => 'El campo :attribute debe tener una de las siguientes extensiones: :values.',
    'file' => 'El campo :attribute debe ser un archivo.',
    'filled' => 'El campo :attribute debe tener un valor.',
    'gt' => [
        'array' => 'El campo :attribute debe tener más de :value elementos.',
        'file' => 'El campo :attribute debe ser mayor que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'string' => 'El campo :attribute debe tener más de :value caracteres.',
    ],
    'gte' => [
        'array' => 'El campo :attribute debe tener :value elementos o más.',
        'file' => 'El campo :attribute debe ser mayor o igual que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
        'string' => 'El campo :attribute debe tener :value caracteres o más.',
    ],
    'hex_color' => 'El campo :attribute debe ser un color hexadecimal válido.',
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El valor seleccionado en :attribute no es válido.',
    'in_array' => 'El campo :attribute debe existir en :other.',
    'in_array_keys' => 'El campo :attribute debe contener al menos una de las siguientes claves: :values.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'ip' => 'El campo :attribute debe ser una dirección IP válida.',
    'ipv4' => 'El campo :attribute debe ser una dirección IPv4 válida.',
    'ipv6' => 'El campo :attribute debe ser una dirección IPv6 válida.',
    'json' => 'El campo :attribute debe ser una cadena JSON válida.',
    'list' => 'El campo :attribute debe ser una lista.',
    'lowercase' => 'El campo :attribute debe estar en minúsculas.',
    'lt' => [
        'array' => 'El campo :attribute debe tener menos de :value elementos.',
        'file' => 'El campo :attribute debe ser menor que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor que :value.',
        'string' => 'El campo :attribute debe tener menos de :value caracteres.',
    ],
    'lte' => [
        'array' => 'El campo :attribute no debe tener más de :value elementos.',
        'file' => 'El campo :attribute debe ser menor o igual que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor o igual que :value.',
        'string' => 'El campo :attribute debe tener como máximo :value caracteres.',
    ],
    'mac_address' => 'El campo :attribute debe ser una dirección MAC válida.',
    'max' => [
        'array' => 'El campo :attribute no debe tener más de :max elementos.',
        'file' => 'El campo :attribute no debe ser mayor que :max kilobytes.',
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'string' => 'El campo :attribute no debe tener más de :max caracteres.',
    ],
    'max_digits' => 'El campo :attribute no debe tener más de :max dígitos.',
    'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'mimetypes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
        'file' => 'El campo :attribute debe ser de al menos :min kilobytes.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'min_digits' => 'El campo :attribute debe tener al menos :min dígitos.',
    'missing' => 'El campo :attribute debe faltar.',
    'missing_if' => 'El campo :attribute debe faltar cuando :other es :value.',
    'missing_unless' => 'El campo :attribute debe faltar a menos que :other sea :value.',
    'missing_with' => 'El campo :attribute debe faltar cuando :values está presente.',
    'missing_with_all' => 'El campo :attribute debe faltar cuando :values están presentes.',
    'multiple_of' => 'El campo :attribute debe ser múltiplo de :value.',
    'not_in' => 'El valor seleccionado en :attribute no es válido.',
    'not_regex' => 'El formato del campo :attribute no es válido.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'password' => [
        'letters' => 'El campo :attribute debe contener al menos una letra.',
        'mixed' => 'El campo :attribute debe contener al menos una letra mayúscula y una minúscula.',
        'numbers' => 'El campo :attribute debe contener al menos un número.',
        'symbols' => 'El campo :attribute debe contener al menos un símbolo.',
        'uncompromised' => 'El :attribute dado ha aparecido en una filtración de datos. Por favor elige otro :attribute.',
    ],
    'present' => 'El campo :attribute debe estar presente.',
    'present_if' => 'El campo :attribute debe estar presente cuando :other es :value.',
    'present_unless' => 'El campo :attribute debe estar presente a menos que :other sea :value.',
    'present_with' => 'El campo :attribute debe estar presente cuando :values está presente.',
    'present_with_all' => 'El campo :attribute debe estar presente cuando :values están presentes.',
    'prohibited' => 'El campo :attribute está prohibido.',
    'prohibited_if' => 'El campo :attribute está prohibido cuando :other es :value.',
    'prohibited_if_accepted' => 'El campo :attribute está prohibido cuando :other es aceptado.',
    'prohibited_if_declined' => 'El campo :attribute está prohibido cuando :other es rechazado.',
    'prohibited_unless' => 'El campo :attribute está prohibido a menos que :other esté en :values.',
    'prohibits' => 'El campo :attribute prohíbe que :other esté presente.',
    'regex' => 'El formato del campo :attribute no es válido.',
    'required' => 'El campo :attribute es obligatorio.',
    'required_array_keys' => 'El campo :attribute debe contener entradas para: :values.',
    'required_if' => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_if_accepted' => 'El campo :attribute es obligatorio cuando :other es aceptado.',
    'required_if_declined' => 'El campo :attribute es obligatorio cuando :other es rechazado.',
    'required_unless' => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with' => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_with_all' => 'El campo :attribute es obligatorio cuando :values están presentes.',
    'required_without' => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de :values están presentes.',
    'same' => 'El campo :attribute debe coincidir con :other.',
    'size' => [
        'array' => 'El campo :attribute debe contener :size elementos.',
        'file' => 'El campo :attribute debe pesar :size kilobytes.',
        'numeric' => 'El campo :attribute debe ser :size.',
        'string' => 'El campo :attribute debe tener :size caracteres.',
    ],
    'starts_with' => 'El campo :attribute debe comenzar con uno de los siguientes valores: :values.',
    'string' => 'El campo :attribute debe ser una cadena de texto.',
    'timezone' => 'El campo :attribute debe ser una zona horaria válida.',
    'unique' => 'La :attribute ya existe.',
    'uploaded' => 'El archivo de :attribute no se pudo subir.',
    'uppercase' => 'El campo :attribute debe estar en mayúsculas.',
    'url' => 'El campo :attribute debe ser una URL válida.',
    'ulid' => 'El campo :attribute debe ser un ULID válido.',
    'uuid' => 'El campo :attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'mensaje-personalizado',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'clave' => 'clave de configuración',
        'valor' => 'valor',
        'nombre' => 'nombre',
        'descripcion' => 'descripción',
        'imagen' => 'imagen',
        'activo' => 'estado',
        'precio' => 'precio',
        'stock_actual' => 'stock actual',
        'codigo' => 'código',
        'sku' => 'SKU',
        'slug' => 'slug',
        'correo' => 'correo',
        'telefono' => 'teléfono',
        'password' => 'contraseña',
    ],

];





// return [

//     /*
//     |--------------------------------------------------------------------------
//     | Validation Language Lines
//     |--------------------------------------------------------------------------
//     |
//     | The following language lines contain the default error messages used by
//     | the validator class. Some of these rules have multiple versions such
//     | as the size rules. Feel free to tweak each of these messages here.
//     |
//     */

//     'accepted' => 'The :attribute field must be accepted.',
//     'accepted_if' => 'The :attribute field must be accepted when :other is :value.',
//     'active_url' => 'The :attribute field must be a valid URL.',
//     'after' => 'The :attribute field must be a date after :date.',
//     'after_or_equal' => 'The :attribute field must be a date after or equal to :date.',
//     'alpha' => 'The :attribute field must only contain letters.',
//     'alpha_dash' => 'The :attribute field must only contain letters, numbers, dashes, and underscores.',
//     'alpha_num' => 'The :attribute field must only contain letters and numbers.',
//     'any_of' => 'The :attribute field is invalid.',
//     'array' => 'The :attribute field must be an array.',
//     'ascii' => 'The :attribute field must only contain single-byte alphanumeric characters and symbols.',
//     'before' => 'The :attribute field must be a date before :date.',
//     'before_or_equal' => 'The :attribute field must be a date before or equal to :date.',
//     'between' => [
//         'array' => 'The :attribute field must have between :min and :max items.',
//         'file' => 'The :attribute field must be between :min and :max kilobytes.',
//         'numeric' => 'The :attribute field must be between :min and :max.',
//         'string' => 'The :attribute field must be between :min and :max characters.',
//     ],
//     'boolean' => 'The :attribute field must be true or false.',
//     'can' => 'The :attribute field contains an unauthorized value.',
//     'confirmed' => 'The :attribute field confirmation does not match.',
//     'contains' => 'The :attribute field is missing a required value.',
//     'current_password' => 'The password is incorrect.',
//     'date' => 'The :attribute field must be a valid date.',
//     'date_equals' => 'The :attribute field must be a date equal to :date.',
//     'date_format' => 'The :attribute field must match the format :format.',
//     'decimal' => 'The :attribute field must have :decimal decimal places.',
//     'declined' => 'The :attribute field must be declined.',
//     'declined_if' => 'The :attribute field must be declined when :other is :value.',
//     'different' => 'The :attribute field and :other must be different.',
//     'digits' => 'The :attribute field must be :digits digits.',
//     'digits_between' => 'The :attribute field must be between :min and :max digits.',
//     'dimensions' => 'The :attribute field has invalid image dimensions.',
//     'distinct' => 'The :attribute field has a duplicate value.',
//     'doesnt_contain' => 'The :attribute field must not contain any of the following: :values.',
//     'doesnt_end_with' => 'The :attribute field must not end with one of the following: :values.',
//     'doesnt_start_with' => 'The :attribute field must not start with one of the following: :values.',
//     'email' => 'The :attribute field must be a valid email address.',
//     'encoding' => 'The :attribute field must be encoded in :encoding.',
//     'ends_with' => 'The :attribute field must end with one of the following: :values.',
//     'enum' => 'The selected :attribute is invalid.',
//     'exists' => 'The selected :attribute is invalid.',
//     'extensions' => 'The :attribute field must have one of the following extensions: :values.',
//     'file' => 'The :attribute field must be a file.',
//     'filled' => 'The :attribute field must have a value.',
//     'gt' => [
//         'array' => 'The :attribute field must have more than :value items.',
//         'file' => 'The :attribute field must be greater than :value kilobytes.',
//         'numeric' => 'The :attribute field must be greater than :value.',
//         'string' => 'The :attribute field must be greater than :value characters.',
//     ],
//     'gte' => [
//         'array' => 'The :attribute field must have :value items or more.',
//         'file' => 'The :attribute field must be greater than or equal to :value kilobytes.',
//         'numeric' => 'The :attribute field must be greater than or equal to :value.',
//         'string' => 'The :attribute field must be greater than or equal to :value characters.',
//     ],
//     'hex_color' => 'The :attribute field must be a valid hexadecimal color.',
//     'image' => 'The :attribute field must be an image.',
//     'in' => 'The selected :attribute is invalid.',
//     'in_array' => 'The :attribute field must exist in :other.',
//     'in_array_keys' => 'The :attribute field must contain at least one of the following keys: :values.',
//     'integer' => 'The :attribute field must be an integer.',
//     'ip' => 'The :attribute field must be a valid IP address.',
//     'ipv4' => 'The :attribute field must be a valid IPv4 address.',
//     'ipv6' => 'The :attribute field must be a valid IPv6 address.',
//     'json' => 'The :attribute field must be a valid JSON string.',
//     'list' => 'The :attribute field must be a list.',
//     'lowercase' => 'The :attribute field must be lowercase.',
//     'lt' => [
//         'array' => 'The :attribute field must have less than :value items.',
//         'file' => 'The :attribute field must be less than :value kilobytes.',
//         'numeric' => 'The :attribute field must be less than :value.',
//         'string' => 'The :attribute field must be less than :value characters.',
//     ],
//     'lte' => [
//         'array' => 'The :attribute field must not have more than :value items.',
//         'file' => 'The :attribute field must be less than or equal to :value kilobytes.',
//         'numeric' => 'The :attribute field must be less than or equal to :value.',
//         'string' => 'The :attribute field must be less than or equal to :value characters.',
//     ],
//     'mac_address' => 'The :attribute field must be a valid MAC address.',
//     'max' => [
//         'array' => 'The :attribute field must not have more than :max items.',
//         'file' => 'The :attribute field must not be greater than :max kilobytes.',
//         'numeric' => 'The :attribute field must not be greater than :max.',
//         'string' => 'The :attribute field must not be greater than :max characters.',
//     ],
//     'max_digits' => 'The :attribute field must not have more than :max digits.',
//     'mimes' => 'The :attribute field must be a file of type: :values.',
//     'mimetypes' => 'The :attribute field must be a file of type: :values.',
//     'min' => [
//         'array' => 'The :attribute field must have at least :min items.',
//         'file' => 'The :attribute field must be at least :min kilobytes.',
//         'numeric' => 'The :attribute field must be at least :min.',
//         'string' => 'The :attribute field must be at least :min characters.',
//     ],
//     'min_digits' => 'The :attribute field must have at least :min digits.',
//     'missing' => 'The :attribute field must be missing.',
//     'missing_if' => 'The :attribute field must be missing when :other is :value.',
//     'missing_unless' => 'The :attribute field must be missing unless :other is :value.',
//     'missing_with' => 'The :attribute field must be missing when :values is present.',
//     'missing_with_all' => 'The :attribute field must be missing when :values are present.',
//     'multiple_of' => 'The :attribute field must be a multiple of :value.',
//     'not_in' => 'The selected :attribute is invalid.',
//     'not_regex' => 'The :attribute field format is invalid.',
//     'numeric' => 'The :attribute field must be a number.',
//     'password' => [
//         'letters' => 'The :attribute field must contain at least one letter.',
//         'mixed' => 'The :attribute field must contain at least one uppercase and one lowercase letter.',
//         'numbers' => 'The :attribute field must contain at least one number.',
//         'symbols' => 'The :attribute field must contain at least one symbol.',
//         'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
//     ],
//     'present' => 'The :attribute field must be present.',
//     'present_if' => 'The :attribute field must be present when :other is :value.',
//     'present_unless' => 'The :attribute field must be present unless :other is :value.',
//     'present_with' => 'The :attribute field must be present when :values is present.',
//     'present_with_all' => 'The :attribute field must be present when :values are present.',
//     'prohibited' => 'The :attribute field is prohibited.',
//     'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
//     'prohibited_if_accepted' => 'The :attribute field is prohibited when :other is accepted.',
//     'prohibited_if_declined' => 'The :attribute field is prohibited when :other is declined.',
//     'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
//     'prohibits' => 'The :attribute field prohibits :other from being present.',
//     'regex' => 'The :attribute field format is invalid.',
//     'required' => 'The :attribute field is required.',
//     'required_array_keys' => 'The :attribute field must contain entries for: :values.',
//     'required_if' => 'The :attribute field is required when :other is :value.',
//     'required_if_accepted' => 'The :attribute field is required when :other is accepted.',
//     'required_if_declined' => 'The :attribute field is required when :other is declined.',
//     'required_unless' => 'The :attribute field is required unless :other is in :values.',
//     'required_with' => 'The :attribute field is required when :values is present.',
//     'required_with_all' => 'The :attribute field is required when :values are present.',
//     'required_without' => 'The :attribute field is required when :values is not present.',
//     'required_without_all' => 'The :attribute field is required when none of :values are present.',
//     'same' => 'The :attribute field must match :other.',
//     'size' => [
//         'array' => 'The :attribute field must contain :size items.',
//         'file' => 'The :attribute field must be :size kilobytes.',
//         'numeric' => 'The :attribute field must be :size.',
//         'string' => 'The :attribute field must be :size characters.',
//     ],
//     'starts_with' => 'The :attribute field must start with one of the following: :values.',
//     'string' => 'The :attribute field must be a string.',
//     'timezone' => 'The :attribute field must be a valid timezone.',
//     'unique' => 'The :attribute has already been taken.',
//     'uploaded' => 'The :attribute failed to upload.',
//     'uppercase' => 'The :attribute field must be uppercase.',
//     'url' => 'The :attribute field must be a valid URL.',
//     'ulid' => 'The :attribute field must be a valid ULID.',
//     'uuid' => 'The :attribute field must be a valid UUID.',

//     /*
//     |--------------------------------------------------------------------------
//     | Custom Validation Language Lines
//     |--------------------------------------------------------------------------
//     |
//     | Here you may specify custom validation messages for attributes using the
//     | convention "attribute.rule" to name the lines. This makes it quick to
//     | specify a specific custom language line for a given attribute rule.
//     |
//     */

//     'custom' => [
//         'attribute-name' => [
//             'rule-name' => 'custom-message',
//         ],
//     ],

//     /*
//     |--------------------------------------------------------------------------
//     | Custom Validation Attributes
//     |--------------------------------------------------------------------------
//     |
//     | The following language lines are used to swap our attribute placeholder
//     | with something more reader friendly such as "E-Mail Address" instead
//     | of "email". This simply helps us make our message more expressive.
//     |
//     */

//     'attributes' => [],

// ];
