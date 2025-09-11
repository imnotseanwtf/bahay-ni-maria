<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Location Not Found Message -->
        @if(is_null($this->latitude) || is_null($this->longitude))
            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-yellow-800 font-medium">Location not found</span>
                </div>
                <p class="text-yellow-700 text-sm mt-1">No location data available for this patient.</p>
            </div>
        @endif

        <div 
            id="map" 
            style="height: 400px;" 
            wire:ignore 
            wire:poll.3s="poll"
            x-data="{ 
                mapInstance: null, 
                currentMarker: null,
                initializeMap() {
                    const interval = setInterval(() => {
                        if (window.L) {
                            clearInterval(interval);
                            
                            // Get current coordinates
                            const lat = @js($this->latitude);
                            const lng = @js($this->longitude);
                            
                            // Default coordinates (Calamba)
                            const defaultLat = 14.2113;
                            const defaultLng = 121.1654;
                            
                            // Use actual coordinates if available, otherwise use default
                            const mapLat = lat !== null ? lat : defaultLat;
                            const mapLng = lng !== null ? lng : defaultLng;
                            
                            this.mapInstance = L.map('map').setView([mapLat, mapLng], 13);

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19,
                                attribution: '© OpenStreetMap contributors'
                            }).addTo(this.mapInstance);

                            // Add marker only if we have actual coordinates
                            if (lat !== null && lng !== null) {
                                this.currentMarker = L.marker([lat, lng]).addTo(this.mapInstance);
                                this.currentMarker.bindPopup(`Patient's Last Known Location: {{ \Carbon\Carbon::parse($this->timestamp)->diffForHumans() }}<br>{{ \Carbon\Carbon::parse($this->timestamp)->format('F j, Y g:i A') }}`).openPopup();
                            }
                            
                            // Listen for location updates from Livewire
                            const self = this;
                            window.addEventListener('locationUpdated', function(event) {
                                console.log('Location update received:', event.detail); // Debug log
                                
                                // Handle array of location objects
                                let newLat, newLng;
                                
                                if (Array.isArray(event.detail) && event.detail.length > 0) {
                                    // Get the latest location (last item in array or first item)
                                    const latestLocation = event.detail[event.detail.length - 1];
                                    newLat = latestLocation.latitude;
                                    newLng = latestLocation.longitude;
                                } else if (event.detail && typeof event.detail === 'object') {
                                    // Handle direct object
                                    newLat = event.detail.latitude;
                                    newLng = event.detail.longitude;
                                } else {
                                    console.log('Unexpected event detail format:', event.detail);
                                    return;
                                }
                                
                                console.log('New coordinates:', newLat, newLng); // Debug log
                                
                                // Only update if we have valid coordinates
                                if (newLat !== null && newLat !== undefined && 
                                    newLng !== null && newLng !== undefined) {
                                    console.log('Updating marker with new coordinates'); // Debug log
                                    
                                    // Remove existing marker only if we're adding a new one
                                    if (self.currentMarker) {
                                        self.mapInstance.removeLayer(self.currentMarker);
                                    }
                                    
                                    // Add new marker with latest coordinates
                                    self.currentMarker = L.marker([newLat, newLng]).addTo(self.mapInstance);
                                    self.currentMarker.bindPopup(`Patient's Last Known Location: {{ \Carbon\Carbon::parse($this->timestamp)->diffForHumans() }}<br>{{ \Carbon\Carbon::parse($this->timestamp)->format('F j, Y g:i A') }}`).openPopup();
                                    self.mapInstance.setView([newLat, newLng], 13);
                                } else {
                                    console.log('No valid coordinates, removing existing marker'); // Debug log
                                    
                                    // Remove existing marker when coordinates are null/undefined
                                    if (self.currentMarker) {
                                        self.mapInstance.removeLayer(self.currentMarker);
                                        self.currentMarker = null;
                                    }
                                }
                            });
                        }
                    }, 300);
                }
            }"
            x-init="$nextTick(() => initializeMap())"
        ></div>
    </x-filament::section>
</x-filament-widgets::widget>