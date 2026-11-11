# Tester le thème dans WordPress Playground

Touchez un lien : WordPress s'ouvre dans Safari avec le thème déjà installé.

## Thème seul

[Ouvrir dans WordPress Playground](https://playground.wordpress.net/#%7B%22landingPage%22%3A%22%2F%22%2C%22login%22%3Atrue%2C%22features%22%3A%7B%22networking%22%3Atrue%7D%2C%22steps%22%3A%5B%7B%22step%22%3A%22installTheme%22%2C%22themeData%22%3A%7B%22resource%22%3A%22url%22%2C%22url%22%3A%22https%3A%2F%2Fraw.githubusercontent.com%2Filyvs-7%2FSite-kaftan%2Fclaude%2Fyouthful-gates-02v69a%2Fjolya-caftan.zip%22%7D%2C%22options%22%3A%7B%22activate%22%3Atrue%7D%7D%5D%7D)

## Thème + boutique WooCommerce + 8 caftans

[Ouvrir dans WordPress Playground](https://playground.wordpress.net/#%7B%22landingPage%22%3A%22%2F%22%2C%22login%22%3Atrue%2C%22features%22%3A%7B%22networking%22%3Atrue%7D%2C%22steps%22%3A%5B%7B%22step%22%3A%22installTheme%22%2C%22themeData%22%3A%7B%22resource%22%3A%22url%22%2C%22url%22%3A%22https%3A%2F%2Fraw.githubusercontent.com%2Filyvs-7%2FSite-kaftan%2Fclaude%2Fyouthful-gates-02v69a%2Fjolya-caftan.zip%22%7D%2C%22options%22%3A%7B%22activate%22%3Atrue%7D%7D%2C%7B%22step%22%3A%22installPlugin%22%2C%22pluginData%22%3A%7B%22resource%22%3A%22wordpress.org%2Fplugins%22%2C%22slug%22%3A%22woocommerce%22%7D%2C%22options%22%3A%7B%22activate%22%3Atrue%7D%7D%2C%7B%22step%22%3A%22runPHP%22%2C%22code%22%3A%22%3C%3Fphp%20require%20%27%2Fwordpress%2Fwp-load.php%27%3B%20try%20%7B%20if%20%28%20function_exists%28%27jolya_import_demo%27%29%20%26%26%20%21%20get_option%28%27jolya_demo_done%27%29%20%29%20%7B%20jolya_import_demo%28%29%3B%20%7D%20%7D%20catch%20%28%20Throwable%20%24e%20%29%20%7B%20error_log%28%20%24e-%3EgetMessage%28%29%20%29%3B%20%7D%22%7D%5D%7D)
