import { useState, useEffect } from "react";
import { MapContainer, TileLayer, Marker, Popup } from "react-leaflet";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

// Fix default marker icon issue in Vite/React
import markerIcon2x from "leaflet/dist/images/marker-icon-2x.png";
import markerIcon from "leaflet/dist/images/marker-icon.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";

const Projects = () => {
  const [projects, setProjects] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    const fetchProjects = async () => {
      try {
        const response = await fetch(
          "http://hrntechsolutions.com/backend/api/projects.php",
        );
        const data = await response.json();

        if (data.status === "success") {
          setProjects(data.data);
        } else {
          setError(data.message || "Failed to fetch projects");
        }
        // eslint-disable-next-line no-unused-vars
      } catch (err) {
        setError(
          "Could not connect to the database API. Check if your PHP server is running.",
        );
      } finally {
        setIsLoading(false);
      }
    };

    fetchProjects();
  }, []);

  // State wise locations and map integration
  delete L.Icon.Default.prototype._getIconUrl;
  L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
  });

  const locations = [
    {
      name: "Delhi",
      district: "New Delhi",
      location: "New Delhi, Delhi, India",
      mapLink: "https://www.google.com/maps/place/New+Delhi,+Delhi,+India",
      position: [28.6139, 77.209],
    },
    {
      name: "Mumbai",
      district: "Mumbai",
      location: "Mumbai, Maharashtra, India",
      mapLink: "https://www.google.com/maps/place/Mumbai,+Maharashtra,+India",
      position: [19.076, 72.8777],
    },
    {
      name: "Lucknow",
      district: "Lucknow",
      location: "Lucknow, Uttar Pradesh, India",
      mapLink:
        "https://www.google.com/maps/place/Lucknow,+Uttar+Pradesh,+India",
      position: [26.8467, 80.9462],
    },
  ];
  return (
    <div className="bg-bg-color min-h-screen pb-20">
      {/* Hero / Ongoing Projects */}
      <section
        id="ongoing"
        className="bg-accent text-white py-20 relative overflow-hidden scroll-mt-24"
      >
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
          <h1 className="text-4xl md:text-5xl font-serif font-bold mb-4">
            Ongoing Projects
          </h1>
          <p className="text-xl max-w-2xl mx-auto text-blue-50">
            Discover our active interventions and on-ground activities across
            various geographies.
          </p>
        </div>
      </section>

      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
        {/* Project Highlights */}
        <div className="mb-16">
          <div className="flex items-center gap-3 mb-8">
            <span className="text-3xl">🏢</span>
            <h2 className="text-3xl font-serif text-text-primary">
              Featured Active Projects
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            {isLoading ? (
              <div className="col-span-2 py-12 text-center text-gray-500 flex flex-col items-center">
                <div className="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-4"></div>
                Loading active projects from database...
              </div>
            ) : error ? (
              <div className="col-span-2 py-8 px-6 bg-red-50 text-red-600 rounded-xl border border-red-100 text-center">
                ⚠️ {error}
              </div>
            ) : projects.length === 0 ? (
              <div className="col-span-2 py-12 text-center text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                No active projects found at the moment.
              </div>
            ) : (
              projects.map((project) => (
                <div
                  key={project.id}
                  className="bg-white rounded-xl shadow-sm border border-gray-100 p-8 flex flex-col md:flex-row gap-6 hover:shadow-md transition-shadow"
                >
                  <img
                    src={project.image_url}
                    alt={project.title}
                    className="w-full md:w-48 h-48 object-cover rounded-lg"
                  />
                  <div>
                    <div className="text-xs font-bold text-accent uppercase tracking-wider mb-2">
                      {project.category}
                    </div>
                    <h3 className="text-2xl font-serif font-bold text-text-primary mb-2">
                      {project.title}
                    </h3>
                    <p className="text-gray-600 text-sm mb-4 line-clamp-2">
                      {project.description}
                    </p>
                    <div className="flex items-center gap-2 text-sm text-gray-500 mb-4">
                      <span className="text-base mr-1">📍</span>{" "}
                      {project.location}
                    </div>
                    <button className="bg-primary hover:bg-[#5a6425] text-white px-5 py-2 rounded font-medium text-sm transition-colors">
                      View Details
                    </button>
                  </div>
                </div>
              ))
            )}
          </div>
        </div>

        {/* State/District Map Placeholder & Stats */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div
            id="listings"
            className="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-8 overflow-hidden relative min-h-100 scroll-mt-24"
          >
            <h3 className="text-xl font-serif font-bold text-text-primary mb-6 flex items-center gap-2">
              <span className="text-2xl mr-2">🗺️</span> State-wise /
              District-wise Listings
            </h3>

            <div className="w-full h-80 rounded-lg overflow-hidden">
              <MapContainer
                center={[22.9734, 78.6569]}
                zoom={5}
                scrollWheelZoom={true}
                className="w-full h-full z-0"
              >
                <TileLayer
                  attribution="&copy; OpenStreetMap contributors"
                  url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                />

                {locations.map((location, index) => (
                  <Marker key={index} position={location.position}>
                    <Popup>
                      <div>
                        <h4 className="font-bold">{location.name}</h4>
                        <p>District: {location.district}</p>
                        <p>{location.location}</p>
                        <a
                          href={location.mapLink}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="text-blue-600 underline"
                        >
                          Open in Google Maps
                        </a>
                      </div>
                    </Popup>
                  </Marker>
                ))}
              </MapContainer>
            </div>
          </div>

          <div
            id="impact"
            className="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-24"
          >
            <h3 className="text-xl font-serif font-bold text-text-primary mb-6 flex items-center gap-2">
              <span className="text-2xl mr-2">📊</span> Impact Snapshot
            </h3>
            <ul className="space-y-6">
              <li className="border-b pb-4">
                <div className="text-3xl font-bold text-primary mb-1">12</div>
                <div className="text-sm text-gray-600 uppercase tracking-wide">
                  States Covered
                </div>
              </li>
              <li className="border-b pb-4">
                <div className="text-3xl font-bold text-secondary mb-1">45</div>
                <div className="text-sm text-gray-600 uppercase tracking-wide">
                  Districts Operated In
                </div>
              </li>
              <li className="border-b pb-4">
                <div className="text-3xl font-bold text-accent mb-1">15+</div>
                <div className="text-sm text-gray-600 uppercase tracking-wide">
                  Active Major Projects
                </div>
              </li>
              <li>
                <div className="text-3xl font-bold text-primary mb-1">2M+</div>
                <div className="text-sm text-gray-600 uppercase tracking-wide">
                  Beneficiaries Reached
                </div>
              </li>
            </ul>
          </div>
        </div>
      </section>
    </div>
  );
};

export default Projects;
