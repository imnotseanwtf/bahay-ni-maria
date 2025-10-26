<?php

namespace App\Livewire;

use Filament\Facades\Filament;
use Livewire\Component;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;
use Illuminate\Support\Facades\Auth;

class NotificationMonitor extends Component
{
    public $lastNotificationCount = 0;
    public $lastCheckedId = null;

    public function mount()
    {
        $user = Filament::auth()->user() ?? Auth::user();

        if (! $user) {
            $this->lastNotificationCount = 0;
            $this->lastCheckedId = null;
            return;
        }

        // Get initial notification count
        $this->lastNotificationCount = $user->unreadNotifications()->count();

        $latestNotification = $user->unreadNotifications()->first();

        $this->lastCheckedId = $latestNotification?->id ?? null;
    }

    public function checkForNewNotifications()
    {
        $user = Auth::user();

        // Get the latest unread notification
        $latestNotification = $user->unreadNotifications()->first();

        if ($latestNotification === null) {
            $this->lastCheckedId = null;
        }

        // Check if there's a new notification
        if ($latestNotification && $latestNotification->id !== $this->lastCheckedId) {
            $data = $latestNotification->data;
            
            // Show pop-up notification on screen
            Notification::make()
                ->title($data['title'] ?? 'New Notification')
                ->body($data['body'] ?? '')
                ->danger() // or ->success(), ->warning(), etc based on $data['color']
                ->persistent()
                ->duration(null) // Won't auto-dismiss
                ->actions($this->buildActions($data))
                ->send(); // THIS MAKES IT POP UP!
            
            // Update tracking
            $this->lastCheckedId = $latestNotification->id;
        }

        $this->lastNotificationCount = $user->unreadNotifications()->count();
    }
    
    private function buildActions($data)
    {
        $actions = [];
        
        if (isset($data['actions']) && is_array($data['actions'])) {
            foreach ($data['actions'] as $actionData) {
                $action = NotificationAction::make($actionData['name'] ?? 'action')
                    ->label($actionData['label'] ?? 'View');
                
                if (isset($actionData['url'])) {
                    $action->url($actionData['url']);
                }
                
                $action->button();
                
                $actions[] = $action;
            }
        }
        
        // Add a close button
        $actions[] = NotificationAction::make('close')
            ->label('Dismiss')
            ->color('gray')
            ->close();
        
        return $actions;
    }

    public function render()
    {
        return view('livewire.notification-monitor');
    }
}