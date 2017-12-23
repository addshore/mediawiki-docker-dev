{{- define "install.sh" }}
#!/usr/bin/env bash

declare WORKDIR=$(cd $(dirname $0) && pwd);

composer update --working-dir /var/www/mediawiki
bash ${WORKDIR}/wait-for-it.sh {{ .Values.database.kind }}-svc:{{ .Values.database.port }}
bash ${WORKDIR}/installdbs.sh default
{{- end }}