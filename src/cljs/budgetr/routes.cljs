(ns budgetr.routes
  (:require
    [reitit.frontend :as reitit]))


(def router
  (reitit/router
   [["/" :items]
    ["/about" :categories]]))

(defn path-for [route & [params]]
  (if params
    (:path (reitit/match-by-name router route params))
    (:path (reitit/match-by-name router route))))
