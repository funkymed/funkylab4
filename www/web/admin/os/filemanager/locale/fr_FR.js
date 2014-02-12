/**
 * FileTree Translation : French fr_FR
 *
 * @author  Ing. Jozef Sakáloš
 * @translator   Cyril Pereira
 * @license FileTree Translation file is licensed under the terms of
 * the Open Source LGPL 3.0 license.  Commercial use is permitted to the extent
 * that the code/component(s) do NOT become part of another Open Source or Commercially
 * licensed development library or toolkit without explicit permission.
 * 
 * License details: http://www.gnu.org/licenses/lgpl.html
 */
if(Ext.ux.FileUploader){
    Ext.apply(Ext.ux.FileUploader.prototype, {
        jsonErrorText:'Impossible de lire l\'object JSON',
        unknownErrorText:'Erreur inconnus'
    });
}

if(Ext.ux.UploadPanel){
    Ext.apply(Ext.ux.UploadPanel.prototype, {
        addText:'Ajouter',
        clickRemoveText:'Cliquez pour effacer',
        clickStopText:'Cliquez pour arreter',
        emptyText:'Aucun fichier',
        errorText:'Erreur',
        fileQueuedText:'Le fichier <b>{0}</b> is queued for upload' ,
        fileDoneText:'Le fichier <b>{0}</b> est charg&eacute;',
        fileFailedText:'Le fichier <b>{0}</b> a eu une erreur de chargement',
        fileStoppedText:'Le fichier <b>{0}</b> chargement stopp&eacute;',
        fileUploadingText:'Chargement de fichier<b>{0}</b>',
        removeAllText:'Tout effacer',
        removeText:'Effacer',
        stopAllText:'Tout arreter',
        uploadText:'Charger'
    });
}

if(Ext.ux.FileTreeMenu){
    Ext.apply(Ext.ux.FileTreeMenu.prototype, {
    collapseText: 'Tout fermer',
    deleteKeyName:'Effacer fichier',
    deleteText:'Effacer',
    expandText: 'Expandre tout',
    newdirText:'Nouveau dossier',
    openBlankText:'Ouvrir dans une nouvelle fenetre',
    openDwnldText:'Telecharger',
    openPopupText:'Ouvrir dans une fenetre popup',
    openSelfText:'Ouvrir dans cette fenetre',
    openText:'Ouvrir',
    reloadText:'R<span style="text-decoration:underline">e</span>charger',
    renameText: 'Renommer',
    uploadFileText:'<span style="text-decoration:underline">C</span>harger un fichier',
    uploadText:'Charger'
    });
}

if(Ext.ux.FileTreePanel){
    Ext.apply(Ext.ux.FileTreePanel.prototype, {
        confirmText:'Confirmer',
        deleteText:'Effacer',
        errorText:'Erreur',
        existsText:'le fichier <b>{0}</b> ya existe',
        fileText:'Fichier',
        newdirText:'Nouveau dossier',
        overwriteText:'Remplacer ce fichier ?',
        reallyWantText:'Voulez vous vraiment',
        rootText:'Racine des dossiers'
    });
}
