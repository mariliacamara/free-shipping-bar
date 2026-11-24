#!/bin/bash
# A barra do checkout acompanha a morada: o update_order_review devolve o fragmento #fsb-bar
# com o valor da zona certa. Corre localmente contra um dev, por HTTP.
# Uso: FSB_DEV_AUTH=<user:senha> tests/test-fragment.sh <base_url> <produto> <cp_continente> <falta_continente> <cp_ilhas> <falta_ilhas>
# A senha HTTP do dev não fica no repositório (é público).
set -uo pipefail
base=$1 pid=$2 cp1=$3 falta1=$4 cp2=$5 falta2=$6
auth=${FSB_DEV_AUTH:?defina FSB_DEV_AUTH=user:senha}
tmp=$(mktemp -d); trap 'rm -rf "$tmp"' EXIT
c() { curl -s -u "$auth" -A "Mozilla/5.0 (fsb-test)" -c "$tmp/jar" -b "$tmp/jar" "$@"; }

c -o /dev/null "$base/?add-to-cart=$pid"
c -o "$tmp/checkout.html" "$base/finalizar-compra/"
nonce=$(grep -oE '"update_order_review_nonce":"[a-f0-9]+"' "$tmp/checkout.html" | cut -d'"' -f4)
[ -n "$nonce" ] || { echo "FALHOU  sem nonce no checkout"; exit 1; }

falhas=0
verifica() {
	local cp=$1 falta=$2
	c -o "$tmp/r.json" -X POST "$base/?wc-ajax=update_order_review" \
		--data-urlencode "security=$nonce" --data-urlencode "country=PT" --data-urlencode "s_country=PT" \
		--data-urlencode "postcode=$cp" --data-urlencode "s_postcode=$cp" --data-urlencode "has_full_address=true"
	frag=$(python3 -I -c 'import json,sys; print(json.load(open(sys.argv[1])).get("fragments",{}).get("#fsb-bar",""))' "$tmp/r.json" 2>/dev/null)
	if [[ "$frag" == *FALTAM* && "$frag" == *"$falta"* ]]; then
		echo "OK      cp $cp: fragmento #fsb-bar com falta $falta"
	else
		echo "FALHOU  cp $cp: esperado falta $falta, fragmento: ${frag:0:200}"; falhas=$((falhas+1))
	fi
}
verifica "$cp2" "$falta2"
verifica "$cp1" "$falta1"

c -o /dev/null "$base/carrinho/?remove_item=all" 2>/dev/null
[ $falhas -eq 0 ] && echo "Success: todos os testes passaram" || { echo "Error: $falhas teste(s) falharam"; exit 1; }
