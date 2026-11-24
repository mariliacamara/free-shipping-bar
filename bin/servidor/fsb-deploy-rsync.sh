#!/bin/bash
# Comando forçado da chave de deploy do free-shipping-bar (GitHub Actions).
# Só aceita o rsync de envio para a pasta do plugin na produção ou no dev do site indicado.
# Instalar em ~/bin/fsb-deploy-rsync.sh e, no ~/.ssh/authorized_keys:
#   command="/home/<user>/bin/fsb-deploy-rsync.sh /home/<user>/domains/<dominio>/public_html",no-port-forwarding,no-agent-forwarding,no-X11-forwarding,no-pty ssh-ed25519 AAAA... fsb-deploy
# Ver .github/workflows/deploy.yml.
set -f

SITE="${1:?falta a pasta do site}"
PRODUCAO="$SITE/wp-content/plugins/free-shipping-bar/"
DEV="$SITE/dev/wp-content/plugins/free-shipping-bar/"
CMD="$SSH_ORIGINAL_COMMAND"

case "$CMD" in
	*[\;\&\|\`\$\<\>\(\)]* | *--sender* | *..*/*) ;;
	"rsync --server "*" . $PRODUCAO" | "rsync --server "*" . $DEV") exec $CMD ;;
esac

echo "Recusado: esta chave só envia ficheiros para o free-shipping-bar." >&2
exit 1
