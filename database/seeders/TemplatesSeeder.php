<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'lead_welcome',
                'name' => 'Alta lead',
                'channel' => 'both',
                'whatsapp_template' => 'lead_welcome',
                'subject' => '¡Bienvenido/a, {{customer.name|cliente}}!',
                'content_text' => "Hola {{customer.name|cliente}}, gracias por tu interés. Pronto te contactaremos.",
                'variables' => ['customer.name'],
            ],
            [
                'key' => 'appointment_confirmation',
                'name' => 'Confirmación de cita',
                'channel' => 'both',
                'whatsapp_template' => 'appointment_confirmation',
                'subject' => 'Confirmación de tu cita',
                'content_text' => "Hola {{customer.name|cliente}}, tu cita es el {{appointment.date|fecha}} en {{appointment.location|clínica}}.",
                'variables' => ['customer.name','appointment.date','appointment.location'],
            ],
            [
                'key' => 'appointment_reminder',
                'name' => 'Recordatorio de cita',
                'channel' => 'both',
                'whatsapp_template' => 'appointment_reminder',
                'subject' => 'Recordatorio de tu cita',
                'content_text' => "Recordatorio: cita el {{appointment.date|fecha}} en {{appointment.location|clínica}}.",
                'variables' => ['appointment.date','appointment.location'],
            ],
            [
                'key' => 'appointment_post_visit',
                'name' => 'Post-cita',
                'channel' => 'both',
                'whatsapp_template' => 'appointment_post_visit',
                'subject' => 'Gracias por tu visita',
                'content_text' => "Gracias, {{customer.name|cliente}}. ¿Cómo fue tu experiencia?",
                'variables' => ['customer.name'],
            ],
            [
                'key' => 'appointment_no_show',
                'name' => 'No-show',
                'channel' => 'both',
                'whatsapp_template' => 'appointment_no_show',
                'subject' => 'Te echamos de menos',
                'content_text' => "No pudimos verte en tu cita del {{appointment.date|fecha}}. ¿Reprogramamos?",
                'variables' => ['appointment.date'],
            ],
            [
                'key' => 'appointment_cancelled',
                'name' => 'Cita cancelada',
                'channel' => 'both',
                'whatsapp_template' => 'appointment_cancelled',
                'subject' => 'Cita cancelada',
                'content_text' => "Tu cita del {{appointment.date|fecha}} ha sido cancelada.",
                'variables' => ['appointment.date'],
            ],
            [
                'key' => 'treatment_six_of_six',
                'name' => 'Cierre 6/6',
                'channel' => 'both',
                'whatsapp_template' => 'treatment_six_of_six',
                'subject' => 'Tu tratamiento 6/6',
                'content_text' => "¡Enhorabuena por completar 6/6 sesiones! ¿Agendamos una revisión?",
                'variables' => [],
            ],
        ];

        foreach ($templates as $tpl) {
            Template::updateOrCreate(
                ['key' => $tpl['key']],
                [
                    'name' => $tpl['name'],
                    'channel' => $tpl['channel'],
                    'whatsapp_template' => $tpl['whatsapp_template'] ?? null,
                    'subject' => $tpl['subject'] ?? null,
                    'content_text' => $tpl['content_text'],
                    'content_html' => $tpl['content_html'] ?? null,
                    'variables' => $tpl['variables'] ?? [],
                    'active' => true,
                ]
            );
        }
    }
}


