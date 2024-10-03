<?php

namespace App\Livewire;

use Livewire\Component;
use Homeful\Contacts\Models\Contact;

class ContactLog extends Component
{
    public $id;
    public $encrypted_batch_no;

    public function mount($id): void
    {
        $contact = Contact::find($id);
    }

    public function render()
    {
        return view('livewire.contact-log');
    }
}
