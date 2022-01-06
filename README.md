## Sobre

Pacote Laravel para enviar mensagens instantâneas e com delay via Slack API.

## Instalação

Em andamento.

## Configuração

Configure as variáveis de ambiente no arquivo **.env** com os dados de sua aplicação Slack (Ela precisa de ser configurada corretamente e ter as permissões necessárias para isto).

```
SLACK_TOKEN=XXXXXXXXX
SLACK_USERNAME=XXXXXXXXX
```

Faça a migração para poder salvar o histórico das mensagens enviadas e para poder enviar mensagens com delay.

```bash
php artisan migrate
```

Adicione no seu [Scheduling Queued Job](https://laravel.com/docs/master/scheduling#scheduling-queued-jobs) no arquivo **app/Console/Kernel.php** para enviar as mensagens com delay (Seu servidor precisa estar configurado para enviar estas mensagens via crontab):

```php
use Developes\Slack\Jobs\SendSlackNotifications;

//Dentro do método schedule adicione:
$schedule->job(new SendSlackNotifications)->everyMinute();
```

## Como usar

```php
use Developes\Slack\Facades\Slack;

//Enviar uma mensagem instantânea
Slack::sendMessage('SLACKID', 'Test');

//Enviar uma mensagem em um horário específico, sendAt recebe como parametro um objeto Carbon
Slack::sendAt(now()->addMinutes(5))->sendMessage('SLACKID', 'Test with sendAt');

//Adicionar um tempo para o envio da sua mensagem, valor 'days' configurado por padrão
Slack::delayTo(2)->sendMessage('SLACKID', 'Test with delayTo (days)');
Slack::delayTo(30, 'minutes')->sendMessage('SLACKID', 'Test with delayTo (minutes)');
Slack::delayTo(4, 'hours')->sendMessage('SLACKID', 'Test with delayTo (hours)');
Slack::delayTo(6, 'months')->sendMessage('SLACKID', 'Test with delayTo (months)');
```


