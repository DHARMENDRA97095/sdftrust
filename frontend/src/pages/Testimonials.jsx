import { useEffect, useRef, useState } from "react";

const API_URL = "http://localhost/sdftrust/backend/api/testimonial.php";
const ADMIN_BASE_URL = "http://localhost/sdftrust/backend/admin/";

const makeImageUrl = (path) => {
  if (!path) return "https://via.placeholder.com/150?text=User";

  if (path.startsWith("http://") || path.startsWith("https://")) {
    return path;
  }

  return `${ADMIN_BASE_URL}${path.replace(/^\/+/, "")}`;
};

function Testimonials() {
  const [testimonials, setTestimonials] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const scrollRef = useRef(null);

  useEffect(() => {
    const fetchTestimonials = async () => {
      try {
        const response = await fetch(API_URL);

        if (!response.ok) {
          throw new Error("Failed to fetch testimonials");
        }

        const data = await response.json();

        const rawTestimonials = Array.isArray(data)
          ? data
          : Array.isArray(data.data)
            ? data.data
            : [];

        const normalizedTestimonials = rawTestimonials.map((item, index) => ({
          id: item.id || index,
          name: item.name || "Anonymous",
          title: item.title || "Supporter",
          message: item.message || "No message available.",
          image: makeImageUrl(item.image),
        }));

        setTestimonials(normalizedTestimonials);
      } catch (err) {
        console.error("Error loading stories:", err);
        setError(err.message || "Failed to load testimonials");
      } finally {
        setLoading(false);
      }
    };

    fetchTestimonials();
  }, []);

  useEffect(() => {
    const container = scrollRef.current;
    if (!container || testimonials.length === 0) return;

    let animationFrame;
    const speed = 0.5;

    const scroll = () => {
      container.scrollLeft += speed;

      if (
        container.scrollLeft >=
        container.scrollWidth - container.clientWidth
      ) {
        container.scrollLeft = 0;
      }

      animationFrame = requestAnimationFrame(scroll);
    };

    animationFrame = requestAnimationFrame(scroll);

    return () => {
      if (animationFrame) cancelAnimationFrame(animationFrame);
    };
  }, [testimonials]);

  return (
    <section className="py-10 bg-white">
      <div className="max-w-7xl mx-auto px-4 text-center">
        <h2 className="text-3xl font-serif mb-10">Stories of Impact</h2>

        {loading ? (
          <p className="text-primary font-semibold">Loading testimonials...</p>
        ) : error ? (
          <p className="text-red-500 font-semibold">{error}</p>
        ) : testimonials.length === 0 ? (
          <p className="text-gray-500">No testimonials found.</p>
        ) : (
          <div
            ref={scrollRef}
            className="flex gap-6 overflow-x-auto scrollbar-hide pb-8"
          >
            {testimonials.map((item) => (
              <div
                key={item.id}
                className="min-w-[300px] bg-gray-100 p-6 rounded-2xl flex gap-4 text-left shadow-sm"
              >
                <img
                  src={item.image}
                  alt={item.name}
                  className="w-16 h-16 rounded-full object-cover shrink-0"
                  onError={(e) => {
                    e.currentTarget.src =
                      "https://via.placeholder.com/150?text=User";
                  }}
                />
                <div>
                  <p className="text-sm italic mb-2">"{item.message}"</p>
                  <h4 className="font-bold text-sm">{item.name}</h4>
                  <p className="text-xs text-gray-500">{item.title}</p>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </section>
  );
}

export default Testimonials;
