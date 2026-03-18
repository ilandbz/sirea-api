<?php

// public function enviarNotificacion(array $data, array $files)
// {
//     return DB::transaction(function () use ($data, $files) {
//         $notificacion = Notificacion::create([
//             'expediente_id' => $data['expediente_id'],
//             'receptor_casilla_id' => $data['casilla_id'],
//             'asunto' => $data['asunto'],
//             'mensaje' => $data['mensaje'],
//             'hash_integridad' => $this->generateHash($data, $files),
//         ]);

//         foreach ($files as $file) {
//             $path = $file->store('notificaciones/' . $notificacion->id);
//             $notificacion->adjuntos()->create(['path' => $path, 'name' => $file->getClientOriginalName()]);
//         }

//         // Registrar evento en auditoría
//         $this->audit($notificacion->id, 'DEPOSITO');

//         // Notificar por correo externo (Obligatorio por Ley 31736 Art. 5)
//         Mail::to($notificacion->receptor->email)->send(new AvisoNuevaNotificacion($notificacion));

//         return $notificacion;
//     });
// }
