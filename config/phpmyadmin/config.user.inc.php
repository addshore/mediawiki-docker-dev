<?php

# Show BLOB data as a string not hex.
$cfg['DisplayBinaryAsHex'] = false;

# Show BLOB data in row detail pages.
$cfg['ProtectBinary'] = false;

# Show BLOB data on table browse pages.  Hack to hardcode all requests.
$_REQUEST['display_blob'] = true;