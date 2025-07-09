<x-layouts.plain-app>
    <x-slot:title>Tambah Alamat Baru</x-slot:title>

    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold text-gray-900">Tambah Alamat Baru</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Cari alamat, klik peta, atau geser marker untuk menentukan lokasi.
                </p>
            </div>

            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">
                                {{ session('error') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <form method="POST" action="{{ route('profile.addresses.store') }}" class="p-6"
                      x-data="addressForm()" x-init="initMapAndAutocomplete()">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 lg:gap-8">
                        <div class="flex flex-col space-y-6">
                            <div>
                                <label for="address_search" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-search mr-1"></i>
                                    Cari Alamat atau Gunakan Peta
                                </label>
                                <div class="relative">
                                    <input type="text"
                                           id="address_search"
                                           x-model="searchQuery"
                                           placeholder="Mulai ketik alamat Anda..."
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="label" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tag mr-1"></i>
                                    Label Alamat (Opsional)
                                </label>
                                <input type="text"
                                       id="label"
                                       name="label"
                                       value="{{ old('label') }}"
                                       placeholder="Contoh: Rumah, Kantor, Kos"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('label')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="street_address" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-home mr-1"></i>
                                    Alamat Lengkap <span class="text-red-500">*</span>
                                </label>
                                <textarea id="street_address"
                                          name="street_address"
                                          rows="3"
                                          x-model="formData.street_address"
                                          placeholder="Akan terisi otomatis dari pencarian atau peta"
                                          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                          required></textarea>
                                @error('street_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-city mr-1"></i>
                                        Kota/Kabupaten <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           id="city"
                                           name="city"
                                           x-model="formData.city"
                                           placeholder="Contoh: Jakarta Selatan"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                           required>
                                    @error('city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-map mr-1"></i>
                                        Provinsi <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           id="state"
                                           name="state"
                                           x-model="formData.state"
                                           placeholder="Contoh: DKI Jakarta"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                           required>
                                    @error('state')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-mail-bulk mr-1"></i>
                                        Kode Pos <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           id="postal_code"
                                           name="postal_code"
                                           x-model="formData.postal_code"
                                           placeholder="Contoh: 12345"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                           required>
                                    @error('postal_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-globe mr-1"></i>
                                        Negara <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           id="country"
                                           name="country"
                                           x-model="formData.country"
                                           placeholder="Indonesia"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                           required>
                                    @error('country')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col space-y-6 mt-6 lg:mt-0">
                            <div id="map" class="w-full h-64 lg:h-80 rounded-lg border border-gray-300 cursor-pointer"></div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-map-marker-alt mr-1"></i> Latitude
                                    </label>
                                    <input type="number"
                                           id="latitude"
                                           name="latitude"
                                           x-model="formData.latitude"
                                           step="any"
                                           placeholder="Otomatis dari peta"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-50"
                                           readonly>
                                    @error('latitude')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-map-marker-alt mr-1"></i> Longitude
                                    </label>
                                    <input type="number"
                                           id="longitude"
                                           name="longitude"
                                           x-model="formData.longitude"
                                           step="any"
                                           placeholder="Otomatis dari peta"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-50"
                                           readonly>
                                    @error('longitude')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div x-show="coordinateStatus" class="p-3 rounded-md" :class="coordinateStatus === 'success' ? 'bg-green-50' : 'bg-red-50'" x-transition>
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i :class="coordinateStatus === 'success' ? 'fas fa-check-circle text-green-400' : 'fas fa-exclamation-triangle text-red-400'"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium" :class="coordinateStatus === 'success' ? 'text-green-800' : 'text-red-800'" x-text="coordinateMessage"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 my-6"></div>

                    <div>
                        <div class="flex items-center mb-6">
                            <input type="checkbox"
                                   id="is_default"
                                   name="is_default"
                                   value="1"
                                   {{ old('is_default') ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_default" class="ml-2 block text-sm text-gray-700">
                                <i class="fas fa-star mr-1"></i>
                                Jadikan sebagai alamat utama
                            </label>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('profile.addresses.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Alamat
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function addressForm() {
            return {
                searchQuery: '',
                formData: {
                    street_address: @json(old('street_address', '')),
                    city: @json(old('city', '')),
                    state: @json(old('state', '')),
                    postal_code: @json(old('postal_code', '')),
                    country: @json(old('country', 'Indonesia')),
                    latitude: {{ old('latitude', -6.2088) }},
                    longitude: {{ old('longitude', 106.8456) }}
                },
                coordinateStatus: '',
                coordinateMessage: '',
                map: null,
                marker: null,
                autocomplete: null,
                geocoder: null,

                initMapAndAutocomplete() {
                    if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                        this.setupMap();
                    } else {
                        const script = document.createElement('script');
                        // PERBAIKAN 1: URL API Google Maps yang benar. Ganti YOUR_Maps_API_KEY dengan API Key Anda.
                        script.src = `https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap`;
                        script.defer = true;
                        document.head.appendChild(script);
                        
                        // Membuat fungsi initMap dapat diakses secara global
                        window.initMap = () => {
                            this.setupMap();
                        };
                    }
                },

                setupMap() {
                    const initialPosition = {
                        lat: parseFloat(this.formData.latitude),
                        lng: parseFloat(this.formData.longitude)
                    };

                    this.map = new google.maps.Map(document.getElementById('map'), {
                        center: initialPosition,
                        zoom: 15,
                        mapTypeControl: false,
                        streetViewControl: false,
                    });

                    this.marker = new google.maps.Marker({
                        position: initialPosition,
                        map: this.map,
                        draggable: true
                    });
                    
                    this.geocoder = new google.maps.Geocoder();
                    this.setupAutocomplete();
                    
                    this.marker.addListener('dragend', (event) => {
                        this.reverseGeocode(event.latLng);
                    });
                    
                    this.map.addListener('click', (event) => {
                        this.marker.setPosition(event.latLng);
                        this.reverseGeocode(event.latLng);
                    });
                },

                setupAutocomplete() {
                    const input = document.getElementById('address_search');
                    this.autocomplete = new google.maps.places.Autocomplete(input, {
                        types: ['address'],
                        componentRestrictions: { country: 'id' } // Membatasi pencarian hanya di Indonesia
                    });

                    this.autocomplete.addListener('place_changed', () => {
                        const place = this.autocomplete.getPlace();
                        
                        if (!place.geometry || !place.geometry.location) {
                            this.showStatus('error', 'Lokasi tidak ditemukan. Silakan coba lagi.');
                            return;
                        }

                        this.map.setCenter(place.geometry.location);
                        this.map.setZoom(17);
                        this.marker.setPosition(place.geometry.location);
                        
                        this.fillAddressComponents(place);
                        this.setCoordinates(place.geometry.location);
                        this.showStatus('success', 'Alamat berhasil ditemukan dan diisi otomatis.');
                    });
                },
                
                reverseGeocode(location) {
                    this.setCoordinates(location);
                    this.geocoder.geocode({ 'location': location }, (results, status) => {
                        if (status === 'OK') {
                            if (results[0]) {
                                this.fillAddressComponents(results[0]);
                                this.searchQuery = results[0].formatted_address; // Update search box
                                this.showStatus('success', 'Alamat diperbarui dari lokasi di peta.');
                            } else {
                                this.showStatus('error', 'Tidak ada hasil ditemukan.');
                            }
                        } else {
                            this.showStatus('error', 'Geocoder gagal: ' + status);
                        }
                    });
                },

                fillAddressComponents(place) {
                    this.clearForm();
                    const components = place.address_components;
                    
                    // Mengisi alamat lengkap dari hasil geocoding
                    if (place.formatted_address) {
                        this.formData.street_address = place.formatted_address;
                    }

                    let city = '';
                    let state = '';
                    
                    components.forEach(component => {
                        const types = component.types;
                        // Mencari komponen alamat
                        if (types.includes('administrative_area_level_2')) city = component.long_name;
                        else if (types.includes('administrative_area_level_1')) state = component.long_name;
                        else if (types.includes('postal_code')) this.formData.postal_code = component.long_name;
                        else if (types.includes('country')) this.formData.country = component.long_name;
                    });

                    // Membersihkan "Kota " atau "Kabupaten " dari nama kota
                    this.formData.city = city.replace(/Kota |Kabupaten /g, '');
                    this.formData.state = state;
                },

                setCoordinates(location) {
                    this.formData.latitude = location.lat();
                    this.formData.longitude = location.lng();
                },
                
                clearForm() {
                    this.formData.street_address = '';
                    this.formData.city = '';
                    this.formData.state = '';
                    this.formData.postal_code = '';
                    this.formData.country = 'Indonesia';
                },

                showStatus(status, message) {
                    this.coordinateStatus = status;
                    this.coordinateMessage = message;
                    
                    setTimeout(() => {
                        this.coordinateStatus = '';
                        this.coordinateMessage = '';
                    }, 5000);
                }
            }
        }
    </script>
</x-layouts.plain-app>