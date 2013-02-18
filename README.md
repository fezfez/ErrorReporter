# Ajouter une configuration de ce type dans le fichier application.ini

ErrorReporter.options.projectName = "Toto"
ErrorReporter.options.sender.type = "Mail"
ErrorReporter.options.sender.mail.options.from = "debug@toto.info"
ErrorReporter.options.sender.mail.options.to = "demonchaux.stephane@gmail.com"
ErrorReporter.options.sender.mail.options.toName = "stéphane"

# Capturer les Exception dans l'ErrorController

$client = ErrorReporter\ClientFactory::getInstance();
$client->captureException($errors->exception);

# Capturer les erreur, les shutdown, les fatalError

$errorHandler = ErrorReporter\ErrorHandlerFactory::getInstance();
$errorHandler->registerErrorHandler();
$errorHandler->registerShutdownFunction();
$errorHandler->handleFatalError();
