/**
 * ApoSE
 *
 * @license  GNU GPL
 * @link     https://github.com/suipnice/apose
 */

(function() {
  "use strict";
    let epr_yes = document.getElementById("epr-1");
    if(epr_yes) {
        epr_yes.addEventListener("change", function() {
            // Display "session" options when session mode is on.
            document.getElementById('field-session').classList.remove('is-invisible');
        });
    }

    let epr_no = document.getElementById("epr-0");
    if(epr_no) {
        epr_no.addEventListener("change", function() {
            // Hide "session" options when session mode is off.
            document.getElementById('field-session').classList.add('is-invisible');
        });
    }

    let copyBtn = document.getElementById('copyBtn');
    if(copyBtn) {
        copyBtn.addEventListener('click', function () {
            let copyContent = document.getElementById('arbo2').outerHTML;
            updateClipboard(copyContent);
        }, false);
    }

})();

/**
 * Copy the given parameter to the navivator clipboard
 * @param {*} newClip the content to be copied
 */
function updateClipboard(newClip) {
    navigator.clipboard.writeText(newClip).then(
      function () {
        alert("Le contenu a été copié dans votre presse-papier.\
             Rendez-vous dans votre logiciel de tableur pour y coller le tableau.");
        console.log('Content copied to clipboard.');
      },
      function () {
        alert("Vous devez autoriser cette application à accéder au presse-papier,\
            ou sélectionner et copier directement le tableau.");
      },
    );
  }