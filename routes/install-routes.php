<?php
#
# Routes used in the installation process.
#
# For security after installation, you can remove this file or rename it.

Route::post('/install/save/1','Installer@SaveAdminDetails');
Route::post('/install/save/2','Installer@SaveStoreBasic');
Route::post('/install/save/3','Installer@SaveStoreDetails');
Route::post('/install/prev/1','Installer@ShowAdminPage');
Route::post('/install/prev/2','Installer@ShowStorePage');


