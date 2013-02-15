# Le projet doit étre dans un namespace Corp

# Ajouter une configuration de ce type dans le fichier application.ini

ErrorReporter.options.projectName = "All-ways"
ErrorReporter.options.sender.type = "Mail"
ErrorReporter.options.sender.mail.options.from = "debug@allways.be"
ErrorReporter.options.sender.mail.options.to = "stephane.demonchaux@oxalis-fr.com"
ErrorReporter.options.sender.mail.options.toName = "stéphane"

# Capturer les Exception dans l'ErrorController

$client = \Corp\ErrorReporter\ClientFactory::getInstance();
$client->captureException($errors->exception);

# Capturer les erreur, les shutdown, les fatalError

$errorHandler = \Corp\ErrorReporter\ErrorHandlerFactory::getInstance();
$errorHandler->registerErrorHandler();
$errorHandler->registerShutdownFunction();
$errorHandler->handleFatalError();