<?php

namespace App\Observers;
use App\Models\UpdateLog;
use Homeful\Contacts\Models\Contact;
use Illuminate\Support\Facades\Auth;

class ContactsObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  Homeful\Contacts\Models\Contact  $contact
     * @return void
     */
    public function created(Contact $contact)
    {
        // Perform actions after a User is created
    }



    /**
     * Handle the User "deleted" event.
     *
     * @param  Homeful\Contacts\Models\Contact  $contact
     * @return void
     */
    public function deleted(Contact $contact)
    {
        // Perform actions after a User is deleted
    }
    /**
     * Handle the User "updated" event.
     *
     * @param  Homeful\Contacts\Models\Contact  $contact
     * @return void
     */
    public function updated(Contact $contact)
    {
        // Loop through each attribute of the Contact model
        foreach (array_keys($contact->getAttributes()) as $attr) {
            // Check if the attribute was modified
            if ($contact->isDirty($attr)) {
                $this->compareValues($contact, $attr, $contact->getOriginal($attr), $contact->getAttribute($attr));
            }
        }
    }

    private function compareValues(Contact $contact, $attr, $originalValue, $newValue, $prefix = '')
    {
        // Handle arrays recursively
        if (is_array($originalValue) && is_array($newValue)) {
            $allKeys = array_unique(array_merge(array_keys($originalValue), array_keys($newValue)));

            foreach ($allKeys as $key) {
                $originalKeyValue = $originalValue[$key] ?? null;
                $newKeyValue = $newValue[$key] ?? null;

                // Recurse for nested arrays
                $this->compareValues(
                    $contact,
                    $attr,
                    $originalKeyValue,
                    $newKeyValue,
                    $prefix ? $prefix . '.' . $key : $key
                );
            }
        } else {
            // For non-array values, log the change if there’s a difference
            if ($originalValue !== $newValue) {
                $this->createUpdateLog($contact, Auth::id(), $originalValue, $newValue, $prefix ? $prefix : $attr);
            }
        }
    }


    /**
     * Create a new update log entry.
     *
     * @param  Contact  $contact
     * @param  int|null  $userId
     * @param  string|null  $from
     * @param  string|null  $to
     * @param  string|null  $field  // The specific field being updated
     * @return void
     */
    protected function createUpdateLog(Contact $contact, $userId, $from, $to, $field = null)
    {
        UpdateLog::create([
            'loggable_id' => $contact->id,
            'loggable_type' => Contact::class,
            'user_id' => $userId,
            'field' => $field,  // Log the specific field being updated (e.g., 'first_name' or 'address.street')
            'from' => is_array($from) || is_object($from) ? json_encode($from) : $from,
            'to' => is_array($to) || is_object($to) ? json_encode($to) : $to,
        ]);
    }


}
