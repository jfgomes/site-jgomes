![Transactions Logo](http://127.0.0.1:8000/images/cs/i18n/Laravel_i18n.jpg)

- The goal of this feature is to create a translation system for my Laravel website.
- The default language is English, but it's possible to translate the website into other languages.
- Users with administrative privileges can view and edit all translations in the administration area.

## Diagram overview
![git-branch-protection.png](http://127.0.0.1:8000/images/diagrams/i18n.drawio.png)

## Laravel technical implementation
#### API Route
#### API Controller
#### Translation files
#### Translation files

#### Server less route
![Serverless route](https://jgomes.site/images/cs/redis/serve_less_route.png)

#### Server less view blade template
```
```

#### Server less js to get translations and post updates
```
```

#### Controller for private area to see the translations for admin users
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class I18nController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'result'  => compact('user')
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getTranslations(Request $request): JsonResponse
    {
        // Get all language files in the resources/lang directory
        $langFiles = glob(resource_path('lang') . '/*/*.php');

        // Specify which files are allowed to be returned
        $allowedFiles = [
            'welcome.php',
        ];

        // Initialize an array to store translations for each language
        $languages = [];

        // Iterate over each language file
        foreach ($langFiles as $filePath)
        {
            $fileName = basename($filePath);

            // Check if the file is allowed to be returned
            if (in_array($fileName, $allowedFiles))
            {
                // Extract the language from the file path
                $lang = basename(dirname($filePath));

                // Include the file and get its content as an array
                $translationArray = include $filePath;

                // Check if the content is an array
                if (is_array($translationArray))
                {
                    // Add the keys and values to the languages array
                    $fileNameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                    foreach ($translationArray as $key => $value)
                    {
                        $languages[$fileNameWithoutExtension][$key][$lang] = $value;
                    }
                }
            }
        }

        // Get the authenticated user
        $user = $request->user();

        // Return a JSON response with the user information and the translation data
        return response()->json([
            'result' => compact('user'),
            'data' => $languages
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postTranslations(Request $request): JsonResponse
    {
        try {

            // Getting the request data
            $data = $request->input('data');

            // Checking if the data was received correctly
            if ($data === null)
            {
                // Return a JSON with an error code and a message
                return response()->json([
                    'error' => true,
                    'message' => 'Translation data was not provided.'
                ], 400); // Using status code 400 to indicate a bad request
            }

            // Decoding special characters in the string
            $data = urldecode($data);

            // Separating key-value pairs and converting them into an associative array
            parse_str($data, $dataArray);

            // Initializing an array to store all content by language
            $languageContents = [];

            // Iterating through all translations
            foreach ($dataArray as $key => $value)
            {
                // Splitting the key into parts using '###-###' as delimiter
                $parts = explode('###-###', $key);

                // Getting the file name, language, and token
                $fileName = $parts[0];
                $language = $parts[1];
                $token    = $parts[2];

                // Updating the entry for this language in the content array
                $languageContents[$language][$fileName][$token] = $value;
            }

            // Now that the content for each language is prepared, it needs to be written to the files
            foreach ($languageContents as $language => $files)
            {
                // Each translation file for the same language
                foreach ($files as $fileName => $content)
                {
                    // Creating the full path for the file
                    $filePath = resource_path('lang') . '/' . $language . '/' . $fileName . '.php';

                    // Converting the array back to PHP string format
                    $phpContent = "<?php\n\nreturn " . var_export($content, true) . ";\n";

                    // Writing the content to the file
                    file_put_contents($filePath, $phpContent);
                }
            }

            // If we reached here, everything went well!
            return response()->json([
                'result' => 'Translation update successfully done!'
            ]);
            
        } catch (\Exception $ex) {
            // If we reached here, something went wrong and a JSON with an error code and a message is sent back to the client
            return response()->json([
                'error' => true,
                'message' => $ex->getMessage()
            ], 500); // Using status code 500 to indicate an error
        }
    }
}
```

## Demonstration
#### ( Click on the image to watch the video )
[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=)s
