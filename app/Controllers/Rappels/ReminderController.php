<?php 
   namespace App\Controllers\Rappels;

   use App\Models\TaskModel;
   use Config\Services;
   use App\Controllers\BaseController;
   
   class ReminderController extends BaseController
   {
       public function sendEmailReminders()
       {
           $taskModel = new TaskModel();
   
           $tasks = $taskModel->where('DATEDIFF(echeance_tache, CURDATE()) <=', 1)
                              ->where('etat_tache', 'À faire')
                              ->where('id_user', session()->get('id_user'))
                              ->findAll();

           $email = Services::email(); 
           $email->setFrom('XtrayShow@yahoo.fr', 'SGT'); 
   
           foreach ($tasks as $task) {
               
               $userEmail = model('App\Models\UserModel')->find($task['id_user'])['email_user'];
   
               if (empty($userEmail)) {
                   continue; 
               }
   
               $email->setTo($userEmail);
               $email->setSubject("Rappel : Tâche proche de son échéance");
               $email->setMessage("
                   Bonjour,
                   
                   La tâche \"{$task['titre']}\" est proche de son échéance : {$task['echeance_tache']}.
                   Merci de la terminer rapidement.
                   
                   Description : {$task['description_tache']}
               ");
   
               if ($email->send()) {
                   echo "Email envoyé pour la tâche : {$task['titre']} à {$userEmail}.\n";
               } else {
                   echo "Échec de l'envoi pour la tâche : {$task['titre']} à {$userEmail}.\n";
                   log_message('error', $email->printDebugger(['headers', 'subject', 'body']));
               }
           }
   
           return "Rappels traités.";
       }
   }
