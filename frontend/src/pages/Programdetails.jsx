import { useState, useEffect } from "react";
import { Link, useParams } from "react-router-dom";

const BASE_URL = "http://localhost/sdftrust/backend/admin/";

const makeImageUrl = (path) => {
  if (!path) {
    return "https://via.placeholder.com/1200x800?text=No+Image";
  }

  if (path.startsWith("http://") || path.startsWith("https://")) {
    return path;
  }

  return `${BASE_URL}${path.replace(/^\/+/, "")}`;
};

const ProgramDetails = () => {
  const { slug } = useParams();

  const [program, setProgram] = useState(null);
  const [currentImage, setCurrentImage] = useState(0);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);

  useEffect(() => {
    const fetchProgram = async () => {
      try {
        const response = await fetch(
          "http://localhost/sdftrust/backend/api/programs.php",
        );

        if (!response.ok) {
          throw new Error("Failed to fetch program details");
        }

        const data = await response.json();

        if (data.status !== "success") {
          throw new Error(data.message || "Could not load program data");
        }

        const foundProgram = data.data.find((item) => item.slug === slug);

        if (!foundProgram) {
          throw new Error("Program not found");
        }

        let normalizedImages = [];

        if (
          Array.isArray(foundProgram.images) &&
          foundProgram.images.length > 0
        ) {
          normalizedImages = foundProgram.images.map((img) =>
            makeImageUrl(img),
          );
        } else if (foundProgram.image_url) {
          normalizedImages = [makeImageUrl(foundProgram.image_url)];
        } else {
          normalizedImages = [
            "https://via.placeholder.com/1200x800?text=No+Image",
          ];
        }

        setProgram({
          ...foundProgram,
          images: normalizedImages,
        });
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

    fetchProgram();
  }, [slug]);

  useEffect(() => {
    setCurrentImage(0);

    if (!program || !program.images?.length) return;

    const timer = setInterval(() => {
      setCurrentImage((prev) =>
        prev === program.images.length - 1 ? 0 : prev + 1,
      );
    }, 4000);

    return () => clearInterval(timer);
  }, [program]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-bg-color">
        <div className="text-xl font-bold text-primary animate-pulse">
          Loading program details...
        </div>
      </div>
    );
  }

  if (error || !program) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-bg-color px-4">
        <div className="text-center">
          <h2 className="text-3xl font-bold mb-3 text-red-500">
            Program not found
          </h2>
          <p className="text-gray-600 mb-6">
            {error || "The program you are looking for does not exist."}
          </p>
          <Link
            to="/programs"
            className="inline-block bg-primary text-white px-6 py-3 rounded-lg"
          >
            Back to Programs
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="bg-bg-color min-h-screen">
      <section className="bg-primary text-white py-20">
        <div className="max-w-7xl mx-auto px-4 text-center">
          <div className="inline-block bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-widest mb-4">
            {program.icon || "📌"} {program.slug}
          </div>

          <h1 className="text-4xl md:text-5xl font-serif font-bold mb-4">
            {program.title}
          </h1>

          <p className="text-xl max-w-2xl mx-auto">
            Learn how we are making a difference through our{" "}
            {program.title.toLowerCase()} initiatives.
          </p>
        </div>
      </section>

      <section className="max-w-7xl mx-auto px-4 -mt-10">
        <div className="relative h-[400px] md:h-[700px] rounded-2xl overflow-hidden shadow-xl border-4 border-white bg-gray-200">
          {program.images.map((img, index) => (
            <div
              key={index}
              className={`absolute inset-0 transition-opacity duration-1000 ${
                index === currentImage ? "opacity-100" : "opacity-0"
              }`}
            >
              <img
                src={img}
                alt={`${program.title} ${index + 1}`}
                className="w-full h-full object-cover"
                onError={(e) => {
                  e.currentTarget.src =
                    "https://via.placeholder.com/1200x800?text=Image+Not+Found";
                }}
              />
            </div>
          ))}

          <div className="absolute top-4 left-4 z-10">
            <span className="bg-green-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
              {program.status || "active"}
            </span>
          </div>

          {program.images.length > 1 && (
            <div className="absolute bottom-6 left-0 right-0 flex justify-center gap-2 z-10">
              {program.images.map((_, index) => (
                <button
                  key={index}
                  type="button"
                  onClick={() => setCurrentImage(index)}
                  className={`h-2 rounded-full transition-all duration-300 ${
                    index === currentImage ? "w-8 bg-white" : "w-2 bg-white/50"
                  }`}
                  aria-label={`Go to image ${index + 1}`}
                />
              ))}
            </div>
          )}
        </div>
      </section>

      <section className="py-16 max-w-5xl mx-auto px-4">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-12">
          <div className="md:col-span-1 space-y-6">
            <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
              <h3 className="text-lg font-serif font-bold text-text-primary mb-4 border-b pb-2">
                Impact Summary
              </h3>

              <div className="space-y-4">
                <div className="flex items-center gap-3">
                  <span className="text-2xl">👥</span>
                  <div>
                    <p className="text-xs text-gray-500 uppercase font-bold">
                      Beneficiaries
                    </p>
                    <p className="text-primary font-bold">
                      {program.beneficiaries || "N/A"}
                    </p>
                  </div>
                </div>

                <div className="flex items-center gap-3">
                  <span className="text-2xl">📍</span>
                  <div>
                    <p className="text-xs text-gray-500 uppercase font-bold">
                      Active Regions
                    </p>
                    <p className="text-primary font-bold">
                      {program.regions || "N/A"}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <Link
              to="/donate"
              className="block w-full bg-secondary hover:bg-secondary/90 text-white text-center py-4 rounded-xl font-bold transition-all shadow-md"
            >
              Support This Program
            </Link>
          </div>

          <div className="md:col-span-2">
            <h2 className="text-3xl font-serif text-text-primary mb-6">
              About the Program
            </h2>

            <div className="prose prose-lg text-gray-600 max-w-none">
              {(program.description || "")
                .split("\n")
                .map((paragraph, index) => (
                  <p key={index} className="mb-4 leading-relaxed text-gray-600">
                    {paragraph}
                  </p>
                ))}
            </div>

            <div className="mt-10 p-8 bg-[#E9EFE1] rounded-2xl border border-primary/10">
              <h3 className="font-serif text-xl text-primary mb-2">Our Goal</h3>
              <p className="text-gray-700 italic">
                We aim to expand our reach and ensure that the benefits of{" "}
                {program.title} reach every corner of our target regions by
                2027.
              </p>
            </div>
          </div>
        </div>
      </section>

      <section className="py-12 border-t border-gray-200">
        <div className="max-w-7xl mx-auto px-4 text-center">
          <Link
            to="/programs"
            className="inline-flex items-center gap-2 text-gray-500 hover:text-primary transition-colors font-medium"
          >
            <span>←</span> Back to All Programs
          </Link>
        </div>
      </section>
    </div>
  );
};

export default ProgramDetails;
