# Ajout de l'autload Composer

bootstrap.php

     // auloading composer
    require APPLICATION_PATH . '/../vendor/autoload.php';

# Configuration application.ini

    ErrorReporter.options.projectName = "Toto"
    ErrorReporter.options.sender.type = "Mail"
    ErrorReporter.options.sender.mail.options.from = "debug@toto.info"
    ErrorReporter.options.sender.mail.options.to = "demonchaux.stephane@gmail.com"
    ErrorReporter.options.sender.mail.options.toName = "stéphane"

# Capturer les Exception manuellement

    $client = ErrorReporter\ClientFactory::getInstance();
    $client->captureException($errors->exception);

# Capturer différents type d'erreur

    $errorHandler = ErrorReporter\ErrorHandlerFactory::getInstance();
    $errorHandler->registerErrorHandler(); // ErrorHandler
    $errorHandler->registerShutdownFunction(); // RegisterShutdown
    $errorHandler->handleFatalError(); // FatalError
